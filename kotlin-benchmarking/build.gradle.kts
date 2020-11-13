import org.jetbrains.kotlin.gradle.tasks.KotlinCompile

object Versions {
    const val ktlint = "0.39.0"
    const val diktat = "0.1.3"
}

plugins {
    kotlin("jvm") version "1.4.10"
}

group = "com.github.animeshz"
version = "0.0.1"

repositories {
    mavenCentral()
}

dependencies {
    implementation(kotlin("stdlib-jdk8"))
}

tasks {
    val ktlint: Configuration by configurations.creating
    val diktatConfig: JavaExec.() -> Unit = {
        group = "diktat"
        classpath = ktlint
        main = "com.pinterest.ktlint.Main"

        inputs.files(project.fileTree(mapOf("dir" to "src", "include" to "**/*.kt")))
        outputs.dir("${project.buildDir}/reports/diktat/")

        outputs.upToDateWhen { false }  // Allows to run the task again (otherwise skipped till sources are changed).
        isIgnoreExitValue = true  // Allows to skip the non-zero exit code, can be useful when other tasks depends on linter

        dependencies {
            ktlint("com.pinterest:ktlint:${Versions.ktlint}") {
                exclude("com.pinterest.ktlint", "ktlint-ruleset-standard")
            }

            ktlint("org.cqfn.diktat:diktat-rules:${Versions.diktat}")
        }
    }

    create<JavaExec>("diktatCheck") {
        diktatConfig()
        description = "Check Kotlin code style."

        args = listOf("src/*/kotlin/**/*.kt")    // specify proper path to sources that should be checked here
    }

    create<JavaExec>("diktatFormat") {
        diktatConfig()
        description = "Fix Kotlin code style deviations."

        args = listOf("-F", "src/main/kotlin/**/*.kt")  // specify proper path to sources that should be checked here
    }

    withType<KotlinCompile> {
        kotlinOptions.jvmTarget = "1.8"
    }
}
