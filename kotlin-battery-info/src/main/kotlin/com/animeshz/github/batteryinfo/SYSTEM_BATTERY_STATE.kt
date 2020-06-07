package com.animeshz.github.batteryinfo

import com.sun.jna.*

/**
 * Internal Object for pulling data from the OS
 */
@Suppress("LeakingThis", "ClassName")
class SYSTEM_BATTERY_STATE : Structure(ALIGN_MSVC), Structure.ByReference {
    var acOnLine: Byte = 0
    var batteryPresent: Byte = 0
    var charging: Byte = 0
    var discharging: Byte = 0

    var spare0: Byte = 0
    var spare1: Byte = 0
    var spare2: Byte = 0
    var spare3: Byte = 0

    var maxCapacity = 0
    var currentCharge = 0
    var flowRate = 0

    var estimatedTime = 0
    var defaultAlert1 = 0
    var defaultAlert2 = 0

    override fun getFieldOrder(): List<String> {
        return listOf(
            "acOnLine", "batteryPresent", "charging", "discharging",
            "spare0", "spare1", "spare2", "spare3",
            "maxCapacity", "currentCharge", "flowRate",
            "estimatedTime", "defaultAlert1", "defaultAlert2"
        )
    }
}
