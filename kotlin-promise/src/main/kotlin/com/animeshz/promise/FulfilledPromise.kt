package com.animeshz.promise

import kotlin.reflect.KClass

class FulfilledPromise(private var value: Any? = null) : PromiseInterface
{
	init
	{
		if (value is PromiseInterface) throw IllegalArgumentException("You cannot create promise.FulfilledPromise with a promise. Use promise.resolve(promiseOrValue) instead.")
	}

	override fun always(onFulfilledOrRejected: () -> Unit): PromiseInterface
	{
		return this.then { resolve(onFulfilledOrRejected()).then { value } }
	}

	override fun cancel()
	{
	}

	override fun done()
	{
		return
	}

	override fun done(onFulfilled: (Any?) -> Any?)
	{
		val result: Any? = onFulfilled(value)

		if (result is PromiseInterface)
		{
			result.done()
		}
	}

	override fun done(onFulfilled: ((Any?) -> Any?)?, onRejected: (Throwable) -> Any?)
	{
		if (onFulfilled === null) return

		var result: Any?
		try
		{
			 result = onFulfilled(value)
		} catch (exception: Throwable)
		{
			result = reject(exception)
		}

		if (result is RejectedPromise) throw result.reason
		if (result is PromiseInterface) result.done()
	}

	override fun <T : Throwable> otherwise(klass: KClass<T>, onRejected: (T) -> Any?): PromiseInterface
	{
		return this
	}

	override fun otherwise(onRejected: (Throwable) -> Any?): PromiseInterface
	{
		return this
	}

	override fun then(): PromiseInterface
	{
		return this
	}

	override fun then(onFulfilled: (Any?) -> Any?): PromiseInterface
	{
		return Promise({ resolve, reject ->
			try
			{
				resolve(onFulfilled(value))
			}
			catch (exception: Throwable)
			{
				reject(exception)
			}
		})
	}

	override fun then(onFulfilled: ((Any?) -> Any?)?, onRejected: (Throwable) -> Any?): PromiseInterface
	{
		if (onFulfilled == null) return this

		return Promise({ resolve, reject ->
			try
			{
				resolve(onFulfilled(value))
			}
			catch (exception: Throwable)
			{
				reject(exception)
			}
		})
	}
}