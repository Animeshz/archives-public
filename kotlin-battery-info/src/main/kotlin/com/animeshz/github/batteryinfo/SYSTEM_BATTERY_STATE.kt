package com.animeshz.github.batteryinfo

import com.sun.jna.*

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
            "maxCapacity", "remainingCapacity", "rate",
            "estimatedTime", "defaultAlert1", "defaultAlert2"
        )
    }
}
