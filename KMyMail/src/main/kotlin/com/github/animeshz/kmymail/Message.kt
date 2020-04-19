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