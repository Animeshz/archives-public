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


/**
 * @see getBatteryStatus to get an instantaneous object of BatteryState.
 *
 * @param isAcConnected                 Is external power supply connected?
 * @param isBatteryPresent              Is the battery present
 * @param batteryFlow                   Is the battery [BatteryFlow.Charging] or [BatteryFlow.Discharging] or the state is [BatteryFlow.UNKNOWN]
 * @param maxCapacityMWh                Max capacity of the battery in mWh
 * @param currentChargeMWh              Current charge in the battery in mWh
 * @param flowRateMWps                  Flow rate of the battery +ve when charging -ve when discharging, in mW/s
 * @param timeToEmptyFromOS             Time to empty calculated from OS, might be zero (0) if not mentioned.
 * @param timeToEmpty                   Time to empty calculated by [flowRateMWps] and [currentChargeMWh]. It is -1 when battery is charging.
 * @param timeToFull                    Time required to complete charge the battery, calculated via [flowRateMWps] and [currentChargeMWh]. It is -1 when discharging.
 * @param currentChargePercent          Current charge percent (accurate) calculated from [currentChargeMWh] and [maxCapacityMWh].
 * @param currentChargeIntegralPercent  Current charge reported by the OS (Int).
 */
data class BatteryStatus(
    val isAcConnected: Boolean,
    val isBatteryPresent: Boolean,
    val batteryFlow: BatteryFlow,
    val maxCapacityMWh: Int,
    val currentChargeMWh: Int,
    val flowRateMWps: Int,
    val timeToEmptyFromOS: Int,
    val timeToEmpty: Int,
    val timeToFull: Int,
    val currentChargePercent: Double,
    val currentChargeIntegralPercent: Int
)

/**
 * Represents a battery's flow. [Charging] when battery is charging, [Discharging] when discharging. An [UNKNOWN] state when state can't be known.
 */
enum class BatteryFlow {
    Charging,
    Discharging,
    UNKNOWN
}
