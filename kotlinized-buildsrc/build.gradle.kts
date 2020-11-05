import org.ajoberstar.gradle.git.publish.tasks.GitPublishReset
import org.jetbrains.kotlin.gradle.tasks.KotlinCompile

plugins {
    kotlin("jvm") version "1.4.10"
    id("org.jetbrains.dokka") version "1.4.10.2"
    id("org.ajoberstar.git-publish") version "3.0.0"
    `kotlin-dsl`
}

group = "com.github.animeshz"
version = "0.1"

repositories {
    mavenCentral()
    jcenter()
}

kotlin {
    explicitApi()
}

tasks {
    val ktlint: Configuration by configurations.creating
    val buildDir = rootProject.buildDir
    val docsOutputDir = rootDir.resolve("docs")

    withType<KotlinCompile> {
        kotlinOptions.jvmTarget = "1.8"
    }

    getByName<Delete>("clean") {
        delete(buildDir)
        delete(docsOutputDir)
    }

    dokkaHtml.configure {
        dependsOn(clean)
        outputDirectory.set(docsOutputDir)
    }

    getByName<GitPublishReset>("gitPublishReset") {
        dependsOn(dokkaHtml)
    }

    create<JavaExec>("diktatCheck") {
        group = "diktat"

        inputs.files(project.fileTree(mapOf("dir" to "src", "include" to "**/*.kt")))
        outputs.dir("${project.buildDir}/reports/diktat/")

        description = "Check Kotlin code style."
        classpath = ktlint
        main = "com.pinterest.ktlint.Main"
        args = listOf("src/*/kotlin/**/*.kt")

        outputs.upToDateWhen { false }
        isIgnoreExitValue = true

        dependencies {
            ktlint("com.pinterest:ktlint:0.39.0") {
                exclude("com.pinterest.ktlint", "ktlint-ruleset-standard")
            }

            ktlint("org.cqfn.diktat:diktat-rules:0.1.2")
        }
    }
}

gitPublish {
    repoUri.set("https://github.com/Animeshz/kotlinized-buildsrc.git")
    branch.set("gh-pages")

    contents {
        from(file(rootDir.resolve("docs")))
    }

    commitMessage.set("Update Docs")
}
