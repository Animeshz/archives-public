<h1 align="center">Battery Information Provider (Kotlin)</h1>

<p align="center">
    <a href="https://travis-ci.org/Animeshz/battery-info">
        <img src="https://img.shields.io/travis/Animeshz/battery-info?style=flat-square" alt="Build Status" />
    </a>
    <a href="https://www.codacy.com/manual/Animeshz/battery-info?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Animeshz/battery-info&amp;utm_campaign=Badge_Grade">
        <img src="https://img.shields.io/codacy/grade/37404b3fef2a45fa8859a1030f42dbe7?style=flat-square" alt="Code Quality" />
    </a>
    <a href="https://github.com/Animeshz/battery-info/releases">
        <img src="https://img.shields.io/github/release-date/Animeshz/battery-info?style=flat-square&label=Latest%20Release" alt="Latest Release" />
    </a>
    <a href="https://jitpack.io/#Animeshz/battery-info">
        <img src="https://img.shields.io/jitpack/v/github/Animeshz/battery-info?style=flat-square" alt="Jitpack" />
    </a>
    <img src="https://img.shields.io/github/languages/code-size/Animeshz/battery-info?style=flat-square" alt="Code Size"/>
    <a href="https://github.com/Animeshz/battery-info/blob/master/LICENSE">
        <img src="https://img.shields.io/github/license/Animeshz/battery-info?style=flat-square" alt="License" />
    </a>
</p>
API to idiomatically check the battery status in Kotlin, will be supported / interoperable by other JVM languages as well.

Table of contents
-----------------

1.  [Introduction](#introduction)
2.  [Installation](#installation)
    *   [Gradle](#gradle)
    *   [Maven](#maven)
3.  [QuickStart](#quickstart)
    *   [Get Battery Status](#get-battery-status)
    *   [BatteryStatus class](#batteryStatus-class)
4.  [Documentation](#documentation)
5.  [License](#license)

Introduction
------------
This library basically calls the native Windows API or Linux's command line tools to collect information about the battery. Mac OS is not supported yet because I didn't have any device to test it.

Installation
---
### Gradle
```gradle
repositories {
    maven { url 'https://jitpack.io' }
    // maven("jitpack.io")  // for gradle kotlin dsl script
}

dependencies {
    implementation("com.github.Animeshz:battery-info:0.1")
}
```

### Maven
```xml
<repositories>
    ...
    <repository>
        <id>jitpack.io</id>
        <url>https://jitpack.io</url>
    </repository>
</repositories>

<dependencies>
    <dependency>
        <groupId>com.github.Animeshz</groupId>
        <artifactId>battery-info</artifactId>
        <version>0.1</version>
    </dependency>
</dependencies>
```

QuickStart
---
### Get Battery Status
```kotlin
suspend fun main() {
    val status: BatteryStatus = getBatteryStatus()
}
```

### BatteryStatus class
See the doc of [BatteryStatus](https://animeshz.github.io/battery-info/battery-info/com.github.animeshz.batteryinfo/-battery-status) for better visualization of information.
```kotlin
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
```


Documentation
---
[Documentation of this library is here](https://animeshz.github.io/battery-info/battery-info)

License
---
Released under [MIT License](https://github.com/Animeshz/battery-info/blob/master/LICENSE).
