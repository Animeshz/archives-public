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

import java.io.*
import java.util.concurrent.*


@JvmSynthetic
internal fun getLinuxBatteryStatus(): BatteryStatus? {
    val path = """upower --enumerate""".runCommand() ?: return null

    val parameters = listOf("present", "state", "energy-full", "energy", "energy-rate", "time to empty", "percentage")
    val result = """upower -i ${path.first { "battery_BAT" in it }}""".runCommand() ?: return null

    var isAcConnected = false
    var isBatteryPresent = false
    var batteryFlow = BatteryFlow.UNKNOWN
    var maxCapacity = 0
    var remainingCapacity = 0
    var rate = 0
    var timeToEmptyFromOS = 0
    var percentage = 0

    result.map { it.replace("\\s+".toRegex(), "").split(":") }
        .filter { it[0] in parameters }
        .forEach { (name, value) ->
            when (parameters.indexOf(name)) {
                0 -> isBatteryPresent = value == "yes"
                1 -> batteryFlow = if ("dis" in value) BatteryFlow.Discharging else BatteryFlow.Charging.also { isAcConnected = true }
                2 -> maxCapacity = (value.removeSuffix("Wh").toFloat() * 1000).toInt()
                3 -> remainingCapacity = (value.removeSuffix("Wh").toFloat() * 1000).toInt()
                4 -> rate = (value.removeSuffix("W").toFloat() * 1000).toInt()
                5 -> timeToEmptyFromOS = value.parseTimeToEmptyFromOS()
                6 -> percentage = value.trimEnd('%').toInt()
            }
        }
    if (batteryFlow == BatteryFlow.Discharging) rate *= -1

    val timeToEmpty: Int = if (batteryFlow != BatteryFlow.Discharging) -1 else if (rate == 0) Int.MAX_VALUE else (-remainingCapacity * 3600 / rate.toDouble()).toInt()
    val timeToFull = if (batteryFlow != BatteryFlow.Charging) -1 else if (rate == 0) Int.MAX_VALUE else (maxCapacity - remainingCapacity) * 3600 / rate
    val currentChargePercent = remainingCapacity.toDouble() * 100 / maxCapacity

    return BatteryStatus(
        isAcConnected,
        isBatteryPresent,
        batteryFlow,
        maxCapacity,
        remainingCapacity,
        rate,
        timeToEmptyFromOS,
        timeToEmpty,
        timeToFull,
        currentChargePercent,
        percentage
    )
}

private fun String.runCommand(
    workingDir: File = File("."),
    timeoutAmount: Long = 60,
    timeoutUnit: TimeUnit = TimeUnit.SECONDS
): Sequence<String>? = try {
    ProcessBuilder(split("\\s".toRegex()))
        .directory(workingDir)
        .redirectOutput(ProcessBuilder.Redirect.PIPE)
        .redirectError(ProcessBuilder.Redirect.PIPE)
        .start()
        .apply { waitFor(timeoutAmount, timeoutUnit) }
        .inputStream.bufferedReader().lineSequence()
} catch (e: IOException) {
    e.printStackTrace()
    null
}

private fun String.parseTimeToEmptyFromOS(): Int {
    return when {
        "hour" in this -> substringBefore('h').toDouble() * 3600
        "minute" in this -> substringBefore('m').toDouble() * 60
        "second" in this -> substringBefore('h').toDouble()
        else -> throw IllegalArgumentException("Time to empty should be in the hour(s), minute(s) or second(s)")
    }.toInt()
}
