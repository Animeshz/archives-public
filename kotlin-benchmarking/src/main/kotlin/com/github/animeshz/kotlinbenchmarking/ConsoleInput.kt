/**
 * This benchmarks the different types of input reading techniques used in Kotlin/JVM.
 *
 * In my current setup (AMD Ryzen 5 4500u | 8GB LPDDR4 3200MHz), following are the test results:
 *
 * Enter amount of lines to be tested: 50000
 * For 50000 lines:
 * readUsingReadLine(): 4569 ms
 * readUsingScanner(): 143 ms
 * readUsingBufferedReader(): 17 ms
 */

package com.github.animeshz.kotlin.benchmarking

import java.io.*
import java.util.*
import kotlin.system.*

fun main() {
    val file = File("testing_files/console_input.txt")

    print("Enter amount of lines to be tested: ")
    val totalTestingLines = readLine()!!.toInt()

    file.writeText(createRandomString(totalTestingLines))

    val ipStream = file.inputStream()
    System.setIn(ipStream)

    val time1 = measureTimeMillis {
        readUsingReadLine(totalTestingLines)
    }

    ipStream.channel.position(0)
    val time2 = measureTimeMillis {
        readUsingScanner(totalTestingLines)
    }

    ipStream.channel.position(0)
    val time3 = measureTimeMillis {
        readUsingBufferedReader(totalTestingLines)
    }

    println("For $totalTestingLines lines:")
    println("readUsingReadLine(): $time1 ms")
    println("readUsingScanner(): $time2 ms")
    println("readUsingBufferedReader(): $time3 ms")
}

private fun readUsingReadLine(lines: Int) {
    for (i in 0 until lines) readLine()
}

private fun readUsingScanner(lines: Int) {
    val sc = Scanner(System.`in`)
    for (i in 0 until lines) sc.nextLine()
}

private fun readUsingBufferedReader(lines: Int) {
    val reader = System.`in`.bufferedReader()
    for (i in 0 until lines) reader.readLine()
}

private fun createRandomString(lines: Int): String {
    val sb = StringBuilder()
    for (i in 0 until lines) {
        sb.appendLine(UUID.randomUUID().toString())
    }
    return sb.toString()
}
