import com.jfrog.bintray.gradle.BintrayExtension
import org.ajoberstar.gradle.git.publish.tasks.GitPublishReset
import org.jetbrains.kotlin.gradle.tasks.KotlinCompile

object Library {
    const val group = "com.github.animeshz"
    const val version = "0.1-alpha1"
    const val description =
            "Kotlinized BuildSrc is a Gradle BuildSrc companion for Kotlin based Gradle Scripts in a multi-module projects."
}

plugins {
    kotlin("jvm") version "1.4.10"
    id("org.jetbrains.dokka") version "1.4.10.2"
    id("org.ajoberstar.git-publish") version "3.0.0"
    id("com.jfrog.bintray") version "1.8.5"
    `kotlin-dsl`
    `maven-publish`
}

group = Library.group
version = Library.version

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

    val sourcesJar by registering(Jar::class) {
        archiveClassifier.set("sources")
        from(sourceSets.main.get().allSource)
    }

    configure<BintrayExtension> {
        user = System.getenv("BINTRAY_USER")
        key = System.getenv("BINTRAY_KEY")

        pkg = PackageConfig().apply {
            repo = "maven"
            name = "kotlinized-buildsrc"
            setLicenses("MIT")
            vcsUrl = "https://github.com/Animeshz/kotlinized-buildsrc.git"
            issueTrackerUrl = "https://github.com/Animeshz/kotlinized-buildsrc/issues"

            version = VersionConfig().apply {
                name = Library.version
                desc = Library.description
                vcsTag = Library.version
            }
        }
    }

    configure<PublishingExtension> {
        publications {
            register("kotlinized-buildsrc", MavenPublication::class) {
                from(getComponents()["kotlin"])
                groupId = Library.group
                artifactId = project.name
                version = Library.version

                artifact(sourcesJar.get())
            }
        }
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
