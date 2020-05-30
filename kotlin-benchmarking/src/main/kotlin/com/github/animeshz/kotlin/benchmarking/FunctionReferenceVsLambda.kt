package com.github.animeshz.kotlin.benchmarking

import kotlin.system.*

/**
 * This compares the performance between lambda and reference
 * when passed into a higher order inline function (in this test we use List.map)
 */

/**
 * Creates 5000 lines of 5 digit incrementing with a separating space,
 * Lines and digits can be altered by respective [lines] and [digitsPerLine]
 *
 * Example of return String:
 * ```
 * 1 2 3 4 5
 * 6 7 8 9 10
 * 11 12 13 14 15
 * // ...
 * 24991 24992 24993 24994 24995
 * 24996 24997 24998 24999 25000
 * ```
 */
private fun createStringForTesting(lines: Int = 5000, digitsPerLine: Int = 5): String {
    val builder = StringBuilder()
    sequence {
        var x = 1
        while (true) {
            buildString {
                for (i in 1..digitsPerLine) {
                    append(x++)
                    if (i != digitsPerLine) append(" ")
                }
            }.let { yield(it) }
        }
    }.take(lines).forEach { builder.appendln(it) }

    return builder.toString()
}

private inline fun timeTakenBase(
    input: String,
    iteration: Int,
    fileName: String?,
    crossinline block: (String) -> Unit
): Int {
    val results = mutableListOf<Int>()
    val digitsPerLine: Int = input.reader().useLines { it.first() }.split(" ").count()

    repeat(iteration) {
        input.reader().forEachLine { line ->
            measureNanoTime { block(line) }.toInt().div(digitsPerLine).let { results.add(it) }
        }
    }

    fileName?.let { writeToFile(it, results) }

    return results.average().toInt()
}

/**
 * Tests reference performance (nano-time) with [input] string,
 * writes time taken (in nano-seconds) by each [iteration] file with [fileName] if given.
 *
 * @return average time taken (in nano-second)
 */
private fun timeTakenByReference(input: String, iteration: Int = 5000, fileName: String? = null): Int {
    return timeTakenBase(input, iteration, fileName) { line -> line.split(" ").map(String::toInt) }
}

/**
 * Tests lambda performance (nano-time) with [input] string,
 * writes time taken (in nano-seconds) by each [iteration] file with [fileName] if given.
 *
 * @return average time taken (in nano-second)
 */
private fun timeTakenByLambda(input: String, iteration: Int = 5000, fileName: String? = null): Int {
    return timeTakenBase(input, iteration, fileName) { line -> line.split(" ").map { it.toInt() } }
}

/**
 * Calculates time taken by performing 5 toInt operation 5000 times on 5000 lines
 * each by [timeTakenByReference] and [timeTakenByLambda] and prints the average
 * of the resulting time (in nano-seconds).
 */
fun main() {
    val testString = createStringForTesting()

    // pass fileName named argument to log time taken by each iteration
    val avgTimeForReference = timeTakenByReference(testString)
    val avgTimeForLambda = timeTakenByLambda(testString)

    println("Average time taken for reference: $avgTimeForReference ns")
    println("Average time taken for lambda: $avgTimeForLambda ns")
}
