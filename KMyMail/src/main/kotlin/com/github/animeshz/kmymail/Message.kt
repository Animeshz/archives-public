package com.github.animeshz.kmymail

/**
 * Represents a Message.
 * Parameters are self explanatory
 */
data class Message(
    val id: String,
    val sender: String,
    val subject: String,
    val sentDateFormatted: String,
    val bodyPlainText: String,
    val bodyHtmlContent: String
)
