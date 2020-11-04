package com.github.animeshz.kotlinized.buildsrc

interface CompilerArguments<T : CompilerArguments<T>> {
    val argument: String
}

inline fun <reified T> getCompilerArguments(): List<String> where T : CompilerArguments<T>, T : Enum<T> {
    return enumValues<T>().map { it.argument }
}
