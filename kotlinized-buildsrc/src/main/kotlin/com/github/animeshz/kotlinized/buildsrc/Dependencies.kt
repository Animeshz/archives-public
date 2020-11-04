@file:Suppress("EnumEntryName", "RemoveRedundantBackticks", "unused")

package com.github.animeshz.kotlinized.buildsrc

import org.gradle.api.artifacts.dsl.DependencyHandler

interface Dependencies<T : Dependencies<T>> {
    val implementation: String
    val configuration: Configuration

    enum class Configuration(val configName: String) {
        IMPLEMENTATION("implementation"),
        TEST_IMPLEMENTATION("testImplementation"),
        KAPT("kapt"),
    }
}

inline fun <reified T> DependencyHandler.register() where T : Dependencies<T>, T : Enum<T> {
    enumValues<T>().forEach {
        add(it.configuration.configName, it.implementation)
    }
}
