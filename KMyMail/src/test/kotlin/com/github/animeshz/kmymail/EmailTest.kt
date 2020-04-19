package com.github.animeshz.kmymail

import io.kotest.core.spec.style.*
import io.kotest.matchers.*
import io.kotest.matchers.ints.*
import io.kotest.matchers.string.*

/**
 * Unit tests the Email class
 */
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

    "remainingTime should return value close to 10 minutes" {
        Email(this.coroutineContext).use {
            it.awaitReady()
            val rem = it.remainingTime()
            rem shouldBeLessThan 600
            rem shouldBeGreaterThan 590
        }
    }

    "renew should reset the timeRemaining back to 10 minutes" {
        Email(this.coroutineContext).use {
            it.awaitReady()
            val remBef = it.remainingTime()
            it.renew()
            val remAft = it.remainingTime()
            remAft - remBef shouldBeGreaterThanOrEqual 0
        }
    }

    "parseSingleElementJson should unwrap single key value JsonObject" {
        """{"key": "value"}""".parseSingleElementJson() shouldBe "value"
        """{"key":"value" }""".parseSingleElementJson() shouldBe "value"
        """{"key": 3}""".parseSingleElementJson().toInt() shouldBe 3
    }

})