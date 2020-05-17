/*
 * Copyright 2020 Animesh Sahu <Animesh>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

package com.github.animeshz.kmymail

import com.beust.klaxon.*
import com.github.kittinunf.fuel.*
import com.github.kittinunf.fuel.core.*
import com.github.kittinunf.fuel.coroutines.*
import kotlinx.coroutines.*
import kotlinx.coroutines.channels.*
import mu.*
import java.io.*
import kotlin.coroutines.*

/**
 * Represents an Email
 * @param context Context on which all the coroutine is going to be launched, Job will be overridden.
 */
@Suppress("MemberVisibilityCanBePrivate")
class Email(context: CoroutineContext) : Closeable {
    /**
     * Address of the email.
     *
     * It is late-init, suspend till [awaitReady] to guarantee its been set
     */
    lateinit var address: String
        private set

    /**
     * List of all the fetched messages till now.
     */
    val messages = mutableListOf<Message>()

    /**
     * Broadcast of newly coming messages
     *
     * Listen to it by [BroadcastChannel.openSubscription]
     */
    val messageBroadcast = BroadcastChannel<Message>(Channel.BUFFERED)

    private val klaxon by lazy { Klaxon() }
    private val logger = KotlinLogging.logger {}
    private val job = Job()
    private val scope = CoroutineScope(context + job)
    private val createJob: Job
    private lateinit var fetchJob: Job

    private var stopTime: Long = System.currentTimeMillis()
    private lateinit var cookies: HeaderValues


    init {
        createJob = scope.launch {
            logger.debug { "Generating random email" }
            init()

            scope.launch {
                delay(599_000)
                fetchCancellation()
            }

            fetchJob = scope.launch { fetchJob() }
        }
    }

    /**
     * Suspends caller until [Email] is ready to be used
     */
    suspend fun awaitReady() = createJob.join()

    /**
     * Returns remaining time till the email can be used (in seconds)
     */
    suspend fun remainingTime(): Int {
        val json: String
        try {
            json = requestString(Endpoint.REMAINING_TIME)
        } catch (e: Exception) {
            logger.error(e) { RESPONSE_EXCEPTION_MSG }
            delay(5_000)
            return remainingTime()
        }

        return json.parseSingleElementJson().toInt()
            .apply { stopTime = System.currentTimeMillis() + this }
    }

    /**
     * Checks if this email has been expired.
     */
    suspend fun isExpired(): Boolean {
        val json: String
        try {
            json = requestString(Endpoint.IS_EXPIRED)
        } catch (e: Exception) {
            logger.error(e) { RESPONSE_EXCEPTION_MSG }
            delay(5_000)
            return isExpired()
        }

        return json.parseSingleElementJson().toBoolean()
    }

    /**
     * Email is usable till 10 minutes since being created
     * To get more time (10 minutes from now) call email.renew()
     */
    suspend fun renew() {
        try {
            requestUnit(Endpoint.RESET_TIME).apply {
                stopTime = System.currentTimeMillis() + 599_000
                if (!fetchJob.isActive) {
                    fetchJob = scope.launch { fetchJob() }
                }
            }
        } catch (e: Exception) {
            logger.error(e) { RESPONSE_EXCEPTION_MSG }
            delay(5_000)
            return renew()
        }
    }

    /**
     * Closes all the services running inside this object.
     * Frees up resources.
     */
    override fun close() {
        job.cancel("Closing the email resource")
    }

    /**
     * Returns a string representation of the object.
     */
    override fun toString(): String {
        return "Email(address='$address', messages=$messages)"
    }


    /**
     * Fetches newest messages and updates list of [messages]
     * Notifies all the subscriber of [messageBroadcast] by sending [Message]
     */
    private suspend fun fetchMessages() {
        val fetched = messages.count()
        val json: String
        try {
            json = requestString(Endpoint.MESSAGES, true, fetched)
        } catch (e: Exception) {
            logger.error(e) { RESPONSE_EXCEPTION_MSG }
            delay(5_000)
            return fetchMessages()
        }

        klaxon.parseArray<Message>(json)?.also {
            if (it.isNotEmpty()) {
                for (m in it) m.setCookies(cookies)
                messages.addAll(it)
                scope.launch {
                    for (m in it) {
                        messageBroadcast.send(m)
                    }
                }
            }
        } ?: if (requestString(Endpoint.MESSAGE_COUNT).parseSingleElementJson().toInt() != fetched)
            apply {
                logger.debug { "We are having problem fetching new messages from the server, retrying after 5 seconds" }
                delay(5_000)
                fetchMessages()
            }
    }

    /**
     * Returns a subtype of [Triple] having request, response and result (from response)
     *
     * @param endpoint  Endpoint to which request has to be made
     * @param setCookie whether we have to set the cookies, cookies are received on the
     * @param format    If there is any Java string format available in the endpoint, formats it.
     *
     * Usage:
     * val (request, response, result) = email.request(endpoint)
     */
    private suspend fun request(endpoint: Endpoint, setCookie: Boolean = true, vararg format: Any): ResponseResultOf<String> =
        buildRequest(endpoint, setCookie, format)
            .apply { logger.debug { "Starting string response result request to ${endpoint.value}" } }
            .awaitStringResponseResult()

    /**
     * Returns response [String]
     *
     * @param endpoint  Endpoint to which request has to be made
     * @param setCookie whether we have to set the cookies, cookies are received on the
     * @param format    If there is any Java string format available in the endpoint, formats it.
     */
    private suspend fun requestString(endpoint: Endpoint, setCookie: Boolean = true, vararg format: Any): String =
        buildRequest(endpoint, setCookie, format)
            .apply { logger.debug { "Starting string request to ${endpoint.value}" } }.awaitString()

    /**
     * Runs the request and returns [Unit], use this when result is not useful.
     *
     * @param endpoint  Endpoint to which request has to be made
     * @param setCookie whether we have to set the cookies, cookies are received on the
     * @param format    If there is any Java string format available in the endpoint, formats it.
     */
    private suspend fun requestUnit(endpoint: Endpoint, setCookie: Boolean = true, vararg format: Any): Unit =
        buildRequest(endpoint, setCookie, format)
            .apply { logger.debug { "Starting no return request to ${endpoint.value}" } }
            .awaitUnit()

    private fun buildRequest(endpoint: Endpoint, setCookie: Boolean = true, vararg format: Any) =
        Fuel.get(HTTP + endpoint.value.run { if (format.isNotEmpty()) format(format) else this })
            .apply { if (setCookie) set("cookie", cookies) }

    private suspend fun init() {
        try {
            val (_, response, result) = request(Endpoint.ADDRESS, false)
            cookies = response.headers["Set-Cookie"]

            address = result.get().parseSingleElementJson()
            logger.debug { "Successfully generated random email" }
        } catch (e: Exception) {
            logger.error(e) { RESPONSE_EXCEPTION_MSG }
            delay(5_000)
            return init()
        }
    }

    private suspend fun CoroutineScope.fetchJob() {
        while (isActive) {
            delay(10_000)
            fetchMessages()
        }
    }

    private suspend fun fetchCancellation() {
        val remaining = stopTime - System.currentTimeMillis()
        if (remaining < 0) {
            fetchJob.cancel()
        } else {
            delay(remaining)
            fetchCancellation()
        }
    }

    companion object {
        const val RESPONSE_EXCEPTION_MSG = "Error getting the response, trying again after 5 seconds"
        const val HTTP = "https://10minutemail.com"
    }

    /**
     * Endpoints to fetch the information
     */
    enum class Endpoint(val value: String) {
        ADDRESS("/session/address"),
        REMAINING_TIME("/session/secondsLeft"),
        IS_EXPIRED("/session/expired"),
        RESET_TIME("/session/reset"),
        MESSAGES("/messages/messagesAfter/%d"),
        MESSAGE_COUNT("/messages/messageCount"),
        REPLY("/messages/reply"),
        FORWARD("/messages/forward")
    }
}

/**
 * Uses string operations to extract single element (primitive) from json (object only).
 * It is upto 1700 times faster than normal parsing of json.
 */
fun String.parseSingleElementJson() = substring(indexOf(':')).trim { it in """:" }""" }
