package com.animeshz.github.batteryinfo

import com.sun.jna.*
import kotlinx.coroutines.*
import kotlin.math.*

/**
 * Performs blocking request into IO dispatcher to get [BatteryStatus] from low-level API
 */
suspend fun getBatteryState(): BatteryStatus? {
    return withContext(Dispatchers.IO) {
        val batteryState = SYSTEM_BATTERY_STATE()
        val retrieveValue = PowrProf.CallNtPowerInformation(
            5,
            Pointer.NULL,
            0,
            batteryState,
            batteryState.size().toLong()
        )

        if (retrieveValue == 0) batteryState.toBatteryStatus() else null
    }
}

/**
 * Builds String by applying [block], initial capacity can be altered by [capacity]
 */
inline fun buildString(capacity: Int = 16, block: StringBuilder.() -> Unit): String {
    return StringBuilder(capacity).apply(block).toString()
}

private fun SYSTEM_BATTERY_STATE.toBatteryStatus(): BatteryStatus {
    val batteryFlow = when {
        charging.toInt() != 0 -> BatteryFlow.Charging
        discharging.toInt() != 0 -> BatteryFlow.Discharging
        else -> BatteryFlow.UNKNOWN
    }

    val timeToEmpty = if (batteryFlow != BatteryFlow.Discharging) -1 else (-remainingCapacity * 3600 / rate.toDouble()).toInt()

    val timeToFull = if (batteryFlow != BatteryFlow.Charging) -1 else (maxCapacity - remainingCapacity) * 3600 / rate

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
