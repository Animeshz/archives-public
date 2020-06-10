/*
 * Copyright 2020 Animesh Sahu <Animeshz>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

package com.github.animeshz.osdetector

import java.util.*

/**
 * Types of Operating Systems
 */
enum class OSType {
    Windows,
    MacOS,
    Linux,
    Other
}

/**
 * Architecture of Operating System
 */
@Suppress("EnumEntryName")
enum class OSArchitecture {
    x64,
    x32,
    PowerPc,
    ARM,
    Other
}

/**
 * helper class to check the operating system this Java VM runs in
 *
 * please keep the notes below as a pseudo-license
 *
 * http://stackoverflow.com/questions/228477/how-do-i-programmatically-determine-operating-system-in-java
 * compare to http://svn.terracotta.org/svn/tc/dso/tags/2.6.4/code/base/common/src/com/tc/util/runtime/Os.java
 * http://www.docjar.com/html/api/org/apache/commons/lang/SystemUtils.java.html
 */
@Suppress("unused", "MemberVisibilityCanBePrivate")
object OS {
    /**
     * Lazily computed [OSType] from the os.name System property
     */
    val type: OSType by lazy {
        val os = System.getProperty("os.name", "unknown").toLowerCase(Locale.ENGLISH)
        when {
            "mac" in os || "darwin" in os -> OSType.MacOS
            "win" in os -> OSType.Windows
            "nux" in os || "nix" in os || "aix" in os -> OSType.Linux
            else -> OSType.Other
        }
    }

    /**
     * Lazily computed [OSArchitecture] from the os.arch System property
     */
    val architecture: OSArchitecture by lazy {
        val arch = System.getProperty("os.arch", "unknown")
        when {
            "aarch64" in arch || "arm" in arch -> OSArchitecture.ARM
            "64" in arch -> OSArchitecture.x64
            "86" in arch || "32" in arch -> OSArchitecture.x32
            "powerpc" in arch -> OSArchitecture.PowerPc
            else -> OSArchitecture.Other
        }
    }

    /**
     * Checks if the OS [type] is [OSType.MacOS]
     */
    fun isMac() = type == OSType.MacOS

    /**
     * Checks if the OS [type] is [OSType.Windows]
     */
    fun isWindows() = type == OSType.Windows

    /**
     * Checks if the OS [type] is [OSType.Linux]
     */
    fun isLinux() = type == OSType.Linux

    /**
     * Checks if the OS [architecture] is [OSArchitecture.x64]
     */
    fun isx64() = architecture == OSArchitecture.x64

    /**
     * Checks if the OS [architecture] is [OSArchitecture.x64]
     */
    fun isx32() = architecture == OSArchitecture.x32

    /**
     * Checks if the OS [architecture] is [OSArchitecture.x64]
     */
    fun isArm() = architecture == OSArchitecture.ARM

    /**
     * Checks if the OS [architecture] is [OSArchitecture.x64]
     */
    fun isPowerPc() = architecture == OSArchitecture.PowerPc
}
