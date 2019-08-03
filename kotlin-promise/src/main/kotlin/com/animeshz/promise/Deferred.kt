package com.animeshz.promise

/**
 * Represents a process which may complete or fail in future
 *
 * @constructor optional parameter [canceller] is handover to the promise and called before cancellation of the promise associated (maybe used for closing resources or cleanup of cache).
 * [canceller]
 * @since 1.0
 */
class Deferred(private var canceller: (() -> Unit)? = null)
{
	private var promise: Promise? = null
	private lateinit var resolveCallback: (Any?) -> Any?
	private lateinit var rejectCallback: (Throwable) -> Any?

	/**
	 * Returns the promise associated with this deferred.
	 * State of promise is controlled by [resolve] and [reject]
	 *
	 * @see[resolve]
	 * @see[reject]
	 * @since 1.0
	 * @return[PromiseInterface]
	 */
	fun promise(): PromiseInterface
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

	/**
	 * Resolves the promise associated with this deferred with [value].
	 *
	 * @param[value] the result of the operation you want to feed into the promise
	 * @since 1.0
	 */
	fun resolve(value: Any? = null)
	{
		promise()
		resolveCallback(value)
	}

	/**
	 * Rejects the promise associated with this deferred with [reason]
	 *
	 * @param[reason] Throwable/Exception which explains the process has been failed
	 * @since 1.0
	 */
	fun reject(reason: Throwable)
	{
		promise()
		rejectCallback(reason)
	}
}