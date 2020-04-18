package com.github.animeshz.kmymail

import io.kotest.core.spec.style.*

class EmailTest : StringSpec({
    "create email" {
        Email(this).use {
            it.awaitReady()

        }
    }
})