package com.github.animeshz.kotlinized.buildsrc

/**
 * Represents Compiler Arguments for a submodule.
 *
 * TODO("Add see header to a readme/wiki")
 */
interface CompilerArgument<T : CompilerArgument<T>> {
    /**
     * The compiler argument.
     *
     * Ex: `-Xopt-in=kotlin.ExperimentalStdlibApi`
     */
    val argument: String
}

/**
 * Gets the list of compiler arguments from [CompilerArgument] defined for a submodule specified in [Enum].
 *
 * TODO("Add see header to a readme/wiki")
 */
inline fun <reified T> getCompilerArguments(): List<String> where T : CompilerArgument<T>, T : Enum<T> =
        enumValues<T>().map { it.argument }
