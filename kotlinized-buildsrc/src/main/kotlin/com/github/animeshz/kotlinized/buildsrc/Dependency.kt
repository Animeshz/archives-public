@file:Suppress("EnumEntryName", "RemoveRedundantBackticks", "unused")

package com.github.animeshz.kotlinized.buildsrc

import org.gradle.api.artifacts.dsl.DependencyHandler

/**
 * Represents a Dependency for a submodule.
 *
 * TODO("Add see header to a readme/wiki")
 */
interface Dependency<T : Dependency<T>> {
    /**
     * Dependency notation name for the dependency.
     */
    val dependencyNotation: String

    /**
     * [Configuration] of the dependency.
     */
    val configuration: Configuration

    /**
     * Configurations available from the gradle.
     */
    enum class Configuration(val configName: String) {
        IMPLEMENTATION("implementation"),
        TEST_IMPLEMENTATION("testImplementation"),
        API("api"),
        TEST_API("testApi"),
        COMPILE_ONLY("compileOnly"),
        TEST_COMPILE_ONLY("testCompileOnly"),
        RUNTIME_ONLY("runtimeOnly"),
        TEST_RUNTIME_ONLY("testRuntimeOnly"),
        ANNOTATION_PROCESSOR("annotationProcessor"),
        TEST_ANNOTATION_PROCESSOR("testAnnotationProcessor"),
        KAPT("kapt"),
        ;
    }
}

/**
 * Auto register function to register all the [Repository] of a submodule defined in [Enum].
 */
inline fun <reified T> DependencyHandler.register() where T : Dependency<T>, T : Enum<T> {
    enumValues<T>().forEach {
        add(it.configuration.configName, it.dependencyNotation)
    }
}
