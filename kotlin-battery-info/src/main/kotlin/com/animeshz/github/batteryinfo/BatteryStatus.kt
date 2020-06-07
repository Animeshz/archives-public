package com.animeshz.github.batteryinfo


/**
 * @see getBatteryState to get instantaneous object of BatteryState.
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

enum class BatteryFlow {
    Charging,
    Discharging,
    UNKNOWN
}
