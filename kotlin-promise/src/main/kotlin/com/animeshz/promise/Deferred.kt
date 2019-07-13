package com.animeshz.promise

class Deferred(private var canceller: (() -> Unit)? = null)
{
	private var promise: Promise? = null
	private lateinit var resolveCallback: (Any?) -> Any?
	private lateinit var rejectCallback: (Throwable) -> Any?

	fun promise(): Promise
	{
		var finalPromise = promise

		if (finalPromise === null)
		{
			val canceller = this.canceller
			this.canceller = null
			finalPromise = Promise({ resolve, reject ->
				resolveCallback = resolve
				rejectCallback = reject
				Unit
			}, canceller)
			promise = finalPromise
		}
		return finalPromise
	}

	fun resolve(value: Any? = null)
	{
		promise()
		resolveCallback(value)
	}

	fun reject(reason: Throwable)
	{
		promise()
		rejectCallback(reason)
	}
}