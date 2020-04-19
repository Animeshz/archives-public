package com.github.animeshz.kmymail

import io.kotest.core.spec.style.*
import io.kotest.matchers.*
import io.kotest.matchers.string.*

class EmailTest : StringSpec({

    "email address should match the regex" {
        Email(this.coroutineContext).use {
            it.awaitReady()
            @Suppress("RegExpRedundantEscape")
            it.address shouldMatch Regex("""^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})${'$'}""")
        }
    }

    "isExpired should return false for newly created emails" {
        Email(this.coroutineContext).use {
            it.awaitReady()
            it.isExpired() shouldBe false
        }
    }

    "parseSingleElementJson should unwrap single key value JsonObject" {
        """{"key": "value"}""".parseSingleElementJson() shouldBe "value"
        """{"key":"value" }""".parseSingleElementJson() shouldBe "value"
        """{"key": 3}""".parseSingleElementJson().toInt() shouldBe 3
    }

})