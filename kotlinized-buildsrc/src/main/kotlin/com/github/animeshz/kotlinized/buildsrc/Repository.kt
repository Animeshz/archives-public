package com.github.animeshz.kotlinized.buildsrc

import org.gradle.api.artifacts.ArtifactRepositoryContainer
import org.gradle.api.artifacts.dsl.RepositoryHandler
import org.gradle.kotlin.dsl.maven

/**
 * Represents a Repository for a submodule.
 *
 * TODO("Add see header to a readme/wiki")
 */
public interface Repository<T : Repository<T>> {
    /**
     * Url of the repository
     */
    public val url: String

    public companion object {
        public const val MAVEN_CENTRAL_URL: String = ArtifactRepositoryContainer.MAVEN_CENTRAL_URL
        public const val JCENTER_URL: String = "https://jcenter.bintray.com"
    }
}

/**
 * Auto register function to register all the [Repository] of a submodule defined in [Enum].
 */
public inline fun <reified T> RepositoryHandler.register() where T : Repository<T>, T : Enum<T> {
    enumValues<T>().forEach {
        maven(url = it.url)
    }
}
