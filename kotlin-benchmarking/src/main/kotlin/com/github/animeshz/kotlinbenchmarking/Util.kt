/**
 * This file contains utilities used by the benchmarks.
 */

package com.github.animeshz.kotlinbenchmarking

import java.io.File

/**
 * Writes to file with given [fileName], each element of [collection] in each line.
 * Creates file if not exists.
 */
fun writeToFile(fileName: String, collection: Collection<*>) =
        writeToFile(File(fileName), collection)

/**
 * Writes to [file], each element of [collection] in each line.
 * Creates file if not exists.
 */
fun writeToFile(file: File, collection: Collection<*>) = file
        .apply { createNewFile() }
        .printWriter()
        .apply {
            collection.forEach {
                println(it)
            }
        }
        .close()

/**
 * Builds [String] using [StringBuilder], applying operation from [block].
 */
inline fun buildString(block: StringBuilder.() -> Unit): String =
        StringBuilder().apply(block).toString()
