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

import com.github.animeshz.osdetector.*


/**
 * Performs blocking request into IO dispatcher to get [BatteryStatus] from low-level API.
 * Note this is blocking, you may want to spawn this into Dispatchers.IO if you are using coroutines or a new thread if not.
 */
fun getBatteryStatus(): BatteryStatus? = when {
    OS.isWindows() -> getWindowsBatteryStatus()
    OS.isLinux() -> getLinuxBatteryStatus()
    else -> TODO("Not implemented yet for MacOS")
}
