<h1 align="center">OS Detector (Kotlin)</h1>

<p align="center">
    <a href="https://travis-ci.org/Animeshz/os-detector">
        <img src="https://img.shields.io/travis/Animeshz/os-detector?style=flat-square" alt="Build Status" />
    </a>
    <a href="https://www.codacy.com/manual/Animeshz/os-detector?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Animeshz/os-detector&amp;utm_campaign=Badge_Grade">
        <img src="https://img.shields.io/codacy/grade/37404b3fef2a45fa8859a1030f42dbe7?style=flat-square" alt="Code Quality" />
    </a>
    <a href="https://github.com/Animeshz/os-detector/releases">
        <img src="https://img.shields.io/github/release-date/Animeshz/os-detector?style=flat-square&label=Latest%20Release" alt="Latest Release" />
    </a>
    <a href="https://jitpack.io/#Animeshz/os-detector">
        <img src="https://img.shields.io/jitpack/v/github/Animeshz/os-detector?style=flat-square" alt="Jitpack" />
    </a>
    <img src="https://img.shields.io/github/languages/code-size/Animeshz/os-detector?style=flat-square" alt="Code Size"/>
    <a href="https://github.com/Animeshz/os-detector/blob/master/LICENSE">
        <img src="https://img.shields.io/github/license/Animeshz/os-detector?style=flat-square" alt="License" />
    </a>
</p>
API to idiomatically check the OS type and architecture in Kotlin, will be supported / interoperable by other JVM languages as well.

Table of contents
-----------------

1.  [Introduction](#introduction)
2.  [Installation](#installation)
    *   [Gradle](#gradle)
    *   [Maven](#maven)
3.  [QuickStart](#quickstart)
    *   [Get type of Operating System](#get-type-of-operating-system)
    *   [Get architecture of Operating System](#get-architecture-of-operating-system)
4.  [Documentation](#documentation)
5.  [License](#license)

Introduction
------------
This library basically detects the OS type within Windows, Linux or Mac otherwise marks as Other.
This library detects the system architecture as well within x64, x32, PowerPc, ARM otherwise Other.

Installation
---
### Gradle
```gradle
repositories {
    maven { url 'https://jitpack.io' }
}

dependencies {
    implementation 'com.github.Animeshz:os-detector:0.1'
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
        <artifactId>os-detector</artifactId>
        <version>1.0.1</version>
    </dependency>
</dependencies>
```

QuickStart
---
### Get type of Operating System
```kotlin
fun main() {
    when(OS.type) {
        OSType.Windows -> { /* Execute this block */ }
        OSType.Linux -> { /* Execute this block */ }
        // ...
    }
}
```

### Get architecture of Operating System
```kotlin
fun main() {
    when(OS.architecture) {
        OSArchitecture.x64 -> { /* Execute this block */ }
        OSArchitecture.x32 -> { /* Execute this block */ }
        OSArchitecture.ARM -> { /* Execute this block */ }
        // ...
    }
}
```

### Shorthand functions which are useful
Example: isWindows()
```kotlin
// Same as `OS.type == OSType.Windows`
if (OS.isWindows()) {
    // Execute this block
    println("We are on Windows")
} else {
    // Otherwise execute this block
    println("We are not on Windows")
}
```
To find more shorthand functions to make your life easier, look at the [docs](#documentation).

Documentation
---
[Documentation of this library is here](https://animeshz.github.io/os-detector/os-detector)

License
---
Released under [MIT License](https://github.com/Animeshz/os-detector/blob/master/LICENSE).
