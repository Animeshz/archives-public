import org.jetbrains.kotlin.gradle.tasks.KotlinCompile

plugins {
    kotlin("jvm") version "1.4.10"
    `kotlin-dsl`
}

group = "com.github.animeshz"
version = "0.1"

repositories {
    mavenCentral()
}

tasks.withType<KotlinCompile> {
    kotlinOptions.jvmTarget = "1.8"
}

kotlin {
    explicitApi()
}

val ktlint: Configuration by configurations.creating

tasks.create<JavaExec>("diktatCheck") {
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
