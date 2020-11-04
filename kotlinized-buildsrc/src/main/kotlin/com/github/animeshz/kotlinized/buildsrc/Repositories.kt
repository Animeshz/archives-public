package com.github.animeshz.kotlinized.buildsrc

import org.gradle.api.artifacts.ArtifactRepositoryContainer
import org.gradle.api.artifacts.dsl.RepositoryHandler
import org.gradle.kotlin.dsl.maven

interface Repositories<T : Repositories<T>> {
    val url: String

    companion object {
        const val mavenCentralUrl = ArtifactRepositoryContainer.MAVEN_CENTRAL_URL
        const val jcenterUrl = "https://jcenter.bintray.com"
    }
}

inline fun <reified T> RepositoryHandler.register() where T : Repositories<T>, T : Enum<T> {
    enumValues<T>().forEach {
        maven(url = it.url)
    }
}
