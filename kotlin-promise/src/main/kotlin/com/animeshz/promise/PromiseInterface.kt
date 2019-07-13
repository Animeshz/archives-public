package com.animeshz.promise

import kotlin.reflect.KClass

interface PromiseInterface
{
	fun then(): PromiseInterface

	fun then(onFulfilled: (Any?) -> Any?): PromiseInterface

	fun then(onFulfilled: ((Any?) -> Any?)? = null, onRejected: (Throwable) -> Any?): PromiseInterface

	fun done()

	fun done(onFulfilled: (Any?) -> Any?)

	fun done(onFulfilled: ((Any?) -> Any?)? = null, onRejected: (Throwable) -> Any?)

	fun <T : Throwable> otherwise(klass: KClass<T>, onRejected: (T) -> Any?): PromiseInterface

	fun otherwise(onRejected: (Throwable) -> Any?): PromiseInterface

	fun always(onFulfilledOrRejected: () -> Unit): PromiseInterface

	fun cancel()
}