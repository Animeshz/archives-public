/*
 * Copyright 2020 Animesh Sahu (Animeshz)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

package com.github.animeshz.batteryinfo

import io.kotest.core.spec.style.*
import io.kotest.matchers.*
import io.kotest.matchers.ints.*
import io.kotest.matchers.nulls.*
import kotlin.math.*

class UtilTest : StringSpec({
    "Important fields should not be default values" {
        val batteryStatus = getBatteryStatus()
        batteryStatus.shouldNotBeNull()

        batteryStatus.apply {
            maxCapacityMWh shouldNotBeExactly 0
            currentChargeMWh shouldNotBeExactly 0
            flowRateMWps shouldNotBeExactly 0
        }
    }

    "Calculated fields should not be default values" {
        val batteryStatus = getBatteryStatus()
        batteryStatus.shouldNotBeNull()

        batteryStatus.apply {
            timeToEmpty shouldNotBeExactly 0
            timeToFull shouldNotBeExactly 0
        }
    }

    "The battery's charge percent should be between the greatest integer below it or the smallest integer above it" {
        val batteryStatus = getBatteryStatus()
        batteryStatus.shouldNotBeNull()

        batteryStatus.apply {
            currentChargeIntegralPercent shouldBe between(floor(currentChargePercent).toInt(), ceil(currentChargePercent).toInt())
        }
    }
})
