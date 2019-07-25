Promise
=======
An implementation of promise in kotlin.


Table of contents
-----------------

1. [Introduction](#introduction)
2. [Concepts](#concepts)
    * [Deferred](#deferred)
    * [Promise](#promise-1)
3. [QuickStart](#quickstart)
    * [Deferred](#deferred-1)
    * [PromiseInterface](#promiseinterface)
    * [Promise](#promise-2)
4. [Documentation](#documentation)
5. [Explore More](#explore-more)
6. [License](#license)


Introduction
------------
So, what's the point of promises?

Unlike java, kotlin made the JVM world more easy to read and used so many new and lightweight concepts like sequences, coroutines, etc. I got ahead to implement the promise in kotlin.

Here the promise serves:
* It maybe used as a placeholder for the result of task executing in another thread, see [thread-pool](https://github.com/Animeshz/threadpool)
* A placeholder for the result of a process which may or may not be completed yet
* Code chaining

Concepts
--------
### Deferred
A Deferred represents a computation or unit of work that may not have completed yet. Typically (but not always), that computation will be something that executes asynchronously and completes at some point in the future.
### Promise
While a deferred represents the computation itself, a Promise represents the result of that computation. Thus, each deferred has a promise that acts as a placeholder for its actual result.

> **Note:** A [standalone promise](#promise-2) (promise made by Promise() manually, which does not have a deferred associated) execute and resolves itself at the time of execution synchronously

QuickStart
---

### Deferred
A deferred represents a process which maybe completed in future or may fail.
```kotlin
fun callApiAsync(url: String): PromiseInterface
{
    // create a deferred for the execution of task
    val deferred = Deferred()
    
    // submit the task for asynchronous execution, maybe in another thread?
    // you may call [deferred.resolve(value: Any?)] or [deferred.reject(reason: Throwable)] based on the task is succeed or fail
    submitToQueue(deferred, url)
    
    // return the promise
    return deferred.promise()
}
```
The `promise` method returns the promise of the deferred. The `resolve` and `reject` methods control the state of the promise.

### PromiseInterface
The promise interface provides the common interface for all promise implementations.

A promise represents an eventual outcome, which is either fulfillment (success) and an associated value, or rejection (failure) and an associated reason.

Once in the fulfilled or rejected state, a promise becomes immutable. Neither its state nor its result (or error) can be modified

#### Implementations (read documentation for further info) -
* Promise
* FulfilledPromise
* RejectedPromise

#### See also (read documentation for further info) -
* resolve() - Creating a resolved promise
* reject() - Creating a rejected promise
* done() vs. then()

### Promise
Creates a Standalone Promise whose state is controlled by resolver given into the constructor.
```kotlin
val resolver: ((Any?) -> Any?, (Throwable) -> Any?) -> Any? = { resolve: (Any?) -> Any?, reject: (Throwable) -> Any? ->
    // do some operations
    
    // to return the result to the promise
    resolve(result)
    
    // to reject promise with an Exception
    reject(exception)
    // or even
    throw exception
}

val promise: PromiseInterface = Promise(resolver)
```
Documentation
---
[Documentation of this library is here](https://github.)

Explore More
---
[Thread Pool](https://github.com/Animeshz/threadpool)

License
---
Released under MIT License.