package com.animeshz.promise

import kotlin.reflect.KClass

/**
 * Represents an already fulfilled promise.
 *
 * @constructor [value] is resulting value of the promise. It cannot be an instance of PromiseInterface because there is no guarantee for that promise to transition into fulfilled state.
 * @since 1.0
 */
class FulfilledPromise(private var value: Any? = null) : PromiseInterface
{
	override val state: PromiseState = PromiseState.FULFILLED

	init
	{
		if (value is PromiseInterface) throw IllegalArgumentException("You cannot create promise.FulfilledPromise with a promise. Use promise.resolve(promiseOrValue) instead.")
	}

	override fun always(onFulfilledOrRejected: () -> Unit): PromiseInterface
	{
		return this.then { resolve(onFulfilledOrRejected()).then { value } }
	}

	/**
	 * Calling cancel on fulfilled promise has no effect.
	 *
	 * @since 1.0
	 */
	override fun cancel()
	{
	}

	override fun done()
	{
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

	/**
	 * Returns itself, calling otherwise on fulfilled promise has no effect.
	 *
	 * @since 1.0
	 * @return[PromiseInterface]
	 */
	override fun <T : Throwable> otherwise(klass: KClass<T>, onRejected: (T) -> Any?): PromiseInterface
	{
		return this
	}

	/**
	 * Returns itself, calling otherwise on fulfilled promise has no effect.
	 *
	 * @since 1.0
	 * @return[PromiseInterface]
	 */
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