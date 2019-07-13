package com.animeshz.promise

import kotlin.reflect.KClass
import kotlin.reflect.full.cast
import kotlin.reflect.full.isSubclassOf

class RejectedPromise(internal val reason: Throwable) : PromiseInterface
{
	override fun always(onFulfilledOrRejected: () -> Unit): PromiseInterface
	{
		return this.then(null, { resolve(onFulfilledOrRejected()).then { RejectedPromise(reason) } })
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
		return
	}

	override fun done(onFulfilled: ((Any?) -> Any?)?, onRejected: (Throwable) -> Any?)
	{
		var result: Any?
		try
		{
			result = onRejected(reason)
		} catch (exception: Throwable)
		{
			result = reject(exception)
		}

		if (result is RejectedPromise) throw result.reason
		if (result is PromiseInterface) result.done()
	}

	override fun <T : Throwable> otherwise(klass: KClass<T>, onRejected: (T) -> Any?): PromiseInterface
	{
		return when
		{
			reason::class.isSubclassOf(klass) ->
				Promise({ resolve, reject ->
					try
					{
						resolve(onRejected(klass.cast(reason)))
					}
					catch (exception: Throwable)
					{
						reject(exception)
					}
				})
			else -> this
		}
	}

	override fun otherwise(onRejected: (Throwable) -> Any?): PromiseInterface
	{
		return then(null, { e -> onRejected(e) })
	}

	override fun then(): PromiseInterface
	{
		return this
	}

	override fun then(onFulfilled: (Any?) -> Any?): RejectedPromise
	{
		return this
	}

	override fun then(onFulfilled: ((Any?) -> Any?)?, onRejected: (Throwable) -> Any?): PromiseInterface
	{
		return Promise({ resolve, reject ->
			try
			{
				resolve(onRejected(reason))
			}
			catch (exception: Throwable)
			{
				reject(exception)
			}
		})
	}
}