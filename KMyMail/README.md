KMyMail
=======
<p align="center">
    <img src="https://img.shields.io/github/languages/code-size/Animeshz/KMyMail?style=flat-square" alt="Code Size"/>
 

</p>
![Size](https://img.shields.io/github/languages/code-size/Animeshz/KMyMail?style=flat-square)
Disposable Email API written purely in Kotlin for JVM.

Table of contents
-----------------

1.  [Introduction](#introduction)
2.  [Installation](#installation)
    *   [Maven](#maven)
    *   [Gradle](#gradle)
3.  [QuickStart](#quickstart)
    *   [Create a new email](#create-a-new-email)
    *   [Messages](#messages)
    *   [Time to use](#time-to-use)
    *   [Send reply or forward message](#send-reply-or-forward-message)
    *   [Close Email object to free up your resources](#close-email-object-to-free-up-your-resources)
4.  [Documentation](#documentation)
5.  [License](#license)

Introduction
------------
Ever wondered of temporary mails, receiving messages and then disposing them? This has now been implemented in Kotlin.

This library depends heavily in coroutines, providing a safe and convenient way to use this without blocking your application.

Here's what KMyMail serves:
*   Create disposable temporary emails.
*   Receive mails from it conveniently (from channels as well).
*   Reply to or forward message.
*   CANNOT send mails directly to sb.

Installation
---
### Gradle
First import jitpack.io repository.
```gradle
repositories {
    maven { url 'https://jitpack.io' }
}

dependencies {
    implementation 'com.github.Animeshz:KMyMail:1.0'
}
```

### Maven
First import jitpack.io repository.
```xml
<repositories>
    ...
    <repository>
        <id>jitpack.io</id>
        <url>https://jitpack.io</url>
    </repository>
</repositories>

<dependencies>
    <dependency>
        <groupId>com.github.Animeshz</groupId>
        <artifactId>KMyMail</artifactId>
        <version>1.0</version>
    </dependency>
</dependencies>
```

QuickStart
---
### Create a new email
```kotlin
suspend fun main() = coroutineScope {

    val email = Email(this.coroutineContext) // specify context for email to run on
    
    /**
     * Launches a new coroutine so that main fun does not suspend, and could do work
     */
    launch {
        /**
         * This auto-close email resource by calling email.close().
         */
        email.use {
            it.awaitReady() // <- suspends the coroutine till we're ready to use it
        
            // To get email address
            val address: String = it.address
            
            // Do your tasks with email(it) here 
        }
    }
    // other tasks in main()
}
```

**Note:  if you use `use()` function you can change email.fun to it.fun in following examples**
### Messages
#### Receive messages
```kotlin
val messageReceiver: ReceiveChannel = email.messageBroadcast.openSubscription()
val nextMessage: Message = messageReceiver.receive() // <- suspends till new message has arrived
```

#### To get older messages list
```kotlin
val messages: List<Message> = email.messages
for (message: Message in messages) {
    // use message
}
```

### Time to use
Email object is usable till 10 minutes since being created.

#### To check remaining time
```kotlin
val remainingTime: Int = email.remainingTime()
```

#### To check if it is expired
```kotlin
val isExpired: Boolean = email.isExpired()
```

#### To renew
To renew (make this usable again for 10 minutes, from now)
```kotlin
email.renew()
```

### Send reply or forward message
#### Send reply to Message
To reply to a message (sender)
```kotlin
message.reply("Your message here")
```
#### Forward Message
To forward message to someone
```kotlin
message.forward("addressofperson@asdf.com")
```

### Close Email object to free up your resources
If you don't use `use()` then you have to manually call cancel() on email to close it. It is highly recommended to close it after using it, else it will take system resources.
```kotlin
email.cancel()
```

Documentation
---
[Documentation of this library is here](https://animeshz.github.io/KMyMail/-k-my-mail/)

License
---
Released under [MIT License](https://github.com/Animeshz/KMyMail/blob/master/LICENSE).
