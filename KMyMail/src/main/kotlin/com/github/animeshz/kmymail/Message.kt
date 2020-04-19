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

import com.github.kittinunf.fuel.*
import com.github.kittinunf.fuel.core.*
import kotlinx.coroutines.*
import mu.*

/**
 * Represents a Message.
 * Parameters are self explanatory
 */
@Suppress("MemberVisibilityCanBePrivate")
data class Message(
    val id: String,
    val sender: String,
    val subject: String,
    val sentDateFormatted: String,
    val bodyPlainText: String,
    val bodyHtmlContent: String
) {
    private val logger = KotlinLogging.logger {}
    private lateinit var cookies: HeaderValues

    internal fun setCookies(c: HeaderValues) {
        cookies = c
    }

    /**
     * Replies to the sender of this message
     *
     * @param message the message you want to send as a reply
     */
    suspend fun reply(message: String) {
        try {
            Fuel.post(Email.HTTP + Email.Endpoint.REPLY)
                .body("""{"messageId": $id, "replyBody": $message}""")
                .apply { set("cookie", cookies) }
                .apply { logger.debug { "Sending reply to $sender" } }
                .awaitUnit()
        } catch (e: Exception) {
            logger.error(e) { Email.RESPONSE_EXCEPTION_MSG }
            delay(5_000)
            reply(message)
        }
    }

    /**
     * Forwards this message to sb else
     *
     * @param address email address of person you want to send this message to
     */
    suspend fun forward(address: String) {
        try {
            Fuel.post(Email.HTTP + Email.Endpoint.REPLY)
                .body("""{ "messageId": $id, "forwardAddress": $address}""")
                .apply { set("cookie", cookies) }
                .apply { logger.debug { "Forwarding message to $address" } }
                .awaitUnit()
        } catch (e: Exception) {
            logger.error(e) { Email.RESPONSE_EXCEPTION_MSG }
            delay(5_000)
            forward(address)
        }
    }
}