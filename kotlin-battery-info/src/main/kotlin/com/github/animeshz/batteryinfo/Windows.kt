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

import com.sun.jna.*
import com.sun.jna.win32.*
import kotlin.math.*


@JvmSynthetic
internal fun getWindowsBatteryStatus(): BatteryStatus? {
    val batteryState = SYSTEM_BATTERY_STATE()
    val retrieveValue =
        PowrProf.INSTANCE.CallNtPowerInformation(
            5,
            Pointer.NULL,
            0,
            batteryState,
            batteryState.size().toLong()
        )

    return if (retrieveValue == 0) batteryState.toBatteryStatus() else null
}

private interface PowrProf : StdCallLibrary {
    @Suppress("FunctionName")
    fun CallNtPowerInformation(
        informationLevel: Int,
        inBuffer: Pointer?,
        inBufferLen: Long,
        outBuffer: SYSTEM_BATTERY_STATE?,
        outBufferLen: Long
    ): Int

    companion object {
        val INSTANCE: PowrProf by lazy { Native.load("PowrProf", PowrProf::class.java)!! }
    }
}


/**
 * Internal Object for pulling data from the OS
 */
@Suppress("LeakingThis", "ClassName")
class SYSTEM_BATTERY_STATE : Structure(ALIGN_MSVC), Structure.ByReference {
    @JvmField
    var acOnLine: Byte = 0

    @JvmField
    var batteryPresent: Byte = 0

    @JvmField
    var charging: Byte = 0

    @JvmField
    var discharging: Byte = 0

    @JvmField
    var spare0: Byte = 0

    @JvmField
    var spare1: Byte = 0

    @JvmField
    var spare2: Byte = 0

    @JvmField
    var spare3: Byte = 0

    @JvmField
    var maxCapacity = 0

    @JvmField
    var remainingCapacity = 0

    @JvmField
    var rate = 0

    @JvmField
    var estimatedTime = 0

    @JvmField
    var defaultAlert1 = 0

    @JvmField
    var defaultAlert2 = 0

    override fun getFieldOrder(): List<String> {
        return listOf(
            "acOnLine", "batteryPresent", "charging", "discharging",
            "spare0", "spare1", "spare2", "spare3",
            "maxCapacity", "remainingCapacity", "rate", "estimatedTime",
            "defaultAlert1", "defaultAlert2"
        )
    }
}

private fun SYSTEM_BATTERY_STATE.toBatteryStatus(): BatteryStatus {
    val batteryFlow = when {
        charging.toInt() != 0 -> BatteryFlow.Charging
        discharging.toInt() != 0 -> BatteryFlow.Discharging
        else -> BatteryFlow.UNKNOWN
    }

    val timeToEmpty = if (batteryFlow != BatteryFlow.Discharging) -1 else if (rate == 0) Int.MAX_VALUE else (-remainingCapacity * 3600 / rate.toDouble()).toInt()

    val timeToFull = if (batteryFlow != BatteryFlow.Charging) -1 else if (rate == 0) Int.MAX_VALUE else (maxCapacity - remainingCapacity) * 3600 / rate

    val currentChargePercent = remainingCapacity.toDouble() * 100 / maxCapacity

    return BatteryStatus(
        acOnLine.toInt() != 0,
        batteryPresent.toInt() != 0,
        batteryFlow,
        maxCapacity,
        remainingCapacity,
        rate,
        estimatedTime,
        timeToEmpty,
        timeToFull,
        currentChargePercent,
        currentChargePercent.roundToInt()
    )
}
