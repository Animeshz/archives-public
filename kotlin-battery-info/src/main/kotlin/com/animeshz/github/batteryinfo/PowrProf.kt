package com.animeshz.github.batteryinfo

import com.sun.jna.*
import com.sun.jna.win32.*


interface PowrProf : StdCallLibrary {
    @Suppress("FunctionName")
    fun CallNtPowerInformation(informationLevel: Int, inBuffer: Pointer?, inBufferLen: Long, outBuffer: SYSTEM_BATTERY_STATE?, outBufferLen: Long): Int

    companion object : PowrProf by Native.load("PowrProf", PowrProf::class.java)!!
}
