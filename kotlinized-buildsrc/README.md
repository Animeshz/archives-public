Kotlinized BuildSrc
===================
Kotlinized BuildSrc is a [Gradle BuildSrc](https://docs.gradle.org/current/userguide/organizing_gradle_projects.html#sec:build_sources) companion for Kotlin based Gradle Scripts in a multi-module projects.

__Kotlinized BuildSrc is still in an experimental stage, as such we can't guarantee API stability between releases.__


Table of contents
-----------------

1. [Introduction](#introduction)
2. [Installation](#installation)
    * [Gradle](#gradle-(buildSrc/build.gradle.kts))
3. [QuickStart](#quickstart)
4. [Documentation](#documentation)
5. [License](#license)
6. [Contributing](#contributing)

Introduction
------------
Motivation of the project comes from Gradle's tight integration of defining the scripts for multi-module projects. All the build scripts are scattered in their own directories.
Kotlinized BuildSrc was created as an answer this, it provides simplistic one-file configuration of all the dependencies, repositories, and compiler arguments. 

Installation
------------
### Gradle (buildSrc/build.gradle.kts)
```kotlin
repositories {
    jcenter()
}

dependencies {
    implementation("com.github.animeshz:kotlinized-buildsrc:<version>")
}
```

QuickStart
----------
Get started by defining some repositories and dependencies in your project via creating enums implementing [Dependency](TODO: Link to the docs)
```kotlin
object Repositories {
    enum class TopLevel(override val url: String) : Repository<TopLevel> {
        `maven-central`(Repository.MAVEN_CENTRAL_URL),
    }

    enum class Subprojects(override val url: String) : Repository<Subprojects> {
        `maven-central`(Repository.MAVEN_CENTRAL_URL),
        `jcenter`(Repository.JCENTER_URL),
    }

    enum class MySpecificModule(override val url: String) : Repository<Subprojects> {
        `some-repo`("https://example.com")
    }
}

object Dependencies {
    enum class Subprojects(override val implementation: String, override val configuration: Configuration = Configuration.IMPLEMENTATION) : Dependency<Subprojects> {
        `kotlin-standard-library`("org.jetbrains.kotlin:kotlin-stdlib-jdk8:${Versions.kotlin}"),
        `kotlinx-coroutines`("org.jetbrains.kotlinx:kotlinx-coroutines-core:${Versions.kotlinxCoroutines}"),
        `kotlin-logging`("io.github.microutils:kotlin-logging:${Versions.kotlinLogging}"),

        `kotest-core`("io.kotest:kotest-runner-junit5:${Versions.kotest}", configuration = Configuration.TEST_IMPLEMENTATION),
        `kotest-assertions`("io.kotest:kotest-assertions-core:${Versions.kotest}", configuration = Configuration.TEST_IMPLEMENTATION),
        // ...
    }

    enum class MySpecificModule(override val implementation: String, override val configuration: Configuration = Configuration.IMPLEMENTATION) : Dependency<Subprojects> {
        `specific-library`("...")
    }

}
```

Checkout the docs for more information about [Configuration](https://animeshz.github.io/kotlinized-buildsrc/kotlinized-buildsrc/com.github.animeshz.kotlinized.buildsrc/index.html).

Then call register() on the modules

`build.gradle.kts`:
```kotlin
repositories {
    register<Repositories.TopLevel>()
}

subprojects {
    repositories {
        register<Repositories.Subprojects>()
    }
    
    dependencies {
        register<Dependencies.Subprojects>()
    }
}
```

`myModule/build.gradle.kts`:
```kotlin
repositories {
    register<Repositories.MySpecificModule>()
}

dependencies {
    register<Dependencies.MySpecificModule>()
}
```
The same for other modules as well.

Documentation
-------------
You can visit the [quickstart](#quickstart) to get started, and [Dokka generated docs](https://animeshz.github.io/kotlinized-buildsrc/kotlinized-buildsrc/com.github.animeshz.kotlinized.buildsrc/index.html) for library documentation.

License
-------
This project is licensed under MIT License, a copy of the License is present in the root of the project.

Contributing
------------
Feel free to open a issue or submit a pull request for any bugs/improvements.
