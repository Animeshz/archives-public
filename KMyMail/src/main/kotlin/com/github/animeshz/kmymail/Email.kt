package com.github.animeshz.kmymail

import com.beust.klaxon.*
import com.github.kittinunf.fuel.*
import com.github.kittinunf.fuel.core.*
import com.github.kittinunf.fuel.coroutines.*
import kotlinx.coroutines.*
import kotlinx.coroutines.channels.*
import mu.*
import java.io.*

/**
 * Represents an Email
 * @param coroutineScope Scope on which all the coroutine is going to be launched, Job will be overridden.
 */
@Suppress("MemberVisibilityCanBePrivate")
class Email(coroutineScope: CoroutineScope) : Closeable {
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
    val messageBroadcast = ConflatedBroadcastChannel<Message>()

    private val klaxon by lazy { Klaxon() }
    private val logger = KotlinLogging.logger {}
    private val job = Job()
    private val scope = coroutineScope + job
    private val createJob: Job

    /**
     * Probably add a fetch job for more control instead of always running job for fetching messages.
     * Useful when user forgot to close resources manually by calling [cancel]
     */

    private lateinit var cookies: HeaderValues

    init {
        createJob = scope.launch {
            logger.debug { "Generating random email" }
            init()

            //todo fetch job
            scope.launch {
                while (isActive) {
                    delay(10_000)
                    fetchMessages()
                }
            }
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
    private suspend fun renew() {
        try {
            requestUnit(Endpoint.RESET_TIME)
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
            scope.launch {
                logger.debug { "We are having problem fetching new messages from the server, retrying after 5 seconds" }
                delay(5_000)
                fetchMessages()
            }
    }

    /**
     * Returns a subtype of [Triple] having request, response and result (from response)
     *
     * @param endpoint Endpoint to which request has to be made
     * @param setCookie whether we have to set the cookies, cookies are received on the
     *
     * Usage:
     * val (request, response, result) = email.request(endpoint)
     */
    private suspend fun request(endpoint: Endpoint, setCookie: Boolean = true, vararg format: Any): ResponseResultOf<String> =
        Fuel.get(HTTP + endpoint.value.run { if (format.isNotEmpty()) format(format) })
            .apply { if (setCookie) set("cookie", cookies) }
            .apply { logger.debug { "Starting responseResultString request to ${endpoint.value}" } }
            .awaitStringResponseResult()

    /**
     * Returns response string
     */
    private suspend fun requestString(endpoint: Endpoint, setCookie: Boolean = true, vararg format: Any): String =
        Fuel.get(HTTP + endpoint.value.run { if (format.isNotEmpty()) format(format) })
            .apply { if (setCookie) set("cookie", cookies) }
            .apply { logger.debug { "Starting string request to ${endpoint.value}" } }
            .awaitString()

    private suspend fun requestUnit(endpoint: Endpoint, setCookie: Boolean = true, vararg format: Any): Unit =
        Fuel.get(HTTP + endpoint.value.run { if (format.isNotEmpty()) format(format) })
            .apply { if (setCookie) set("cookie", cookies) }
            .apply { logger.debug { "Starting string request to ${endpoint.value}" } }
            .awaitUnit()

    /**
     * Initializer handled by init block.
     */
    private suspend fun init() {
        val (_, response, result) = request(Endpoint.ADDRESS, false)
        cookies = response.headers["Set-Cookie"]

        try {
            address = result.get().parseSingleElementJson()
            logger.debug { "Successfully generated random email" }
        } catch (e: KotlinNullPointerException) {
            logger.error(e) { "Error parsing address of email, creating new one" }
            init()
        } catch (e: Exception) {
            logger.error(e) { RESPONSE_EXCEPTION_MSG }
            delay(5_000)
            init()
        }
    }

    companion object {
        const val RESPONSE_EXCEPTION_MSG = "Error getting the response, trying again after 5 seconds"

        const val HTTP = "https://10minutemail.com"
    }

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