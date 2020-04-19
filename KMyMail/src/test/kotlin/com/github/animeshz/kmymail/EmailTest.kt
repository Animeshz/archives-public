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