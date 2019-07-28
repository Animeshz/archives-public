package com.animeshz.promise

/**
 * Creates a [FulfilledPromise] with the [value], if the [value] is an instance of [PromiseInterface] it will just return it.
 *
 * @param[value] value which you want to make FulfilledPromise of
 * @since 1.0
 * @return[PromiseInterface]
 */
fun resolve(value: Any? = null): PromiseInterface = if (value is PromiseInterface) value else FulfilledPromise(
	value)

/**
 * Creates a [RejectedPromise] with the [reason].
 *
 * @param[reason] Throwable/Exception which explains the process has been failed
 * @since 1.0
 * @return[RejectedPromise]
 */
fun reject(reason: Throwable) = RejectedPromise(reason)

/**
 * Maps the given [promises] into single promise containing all the results of given [promises].
 * Note: If any of the promise fails in the given [promises], the returning promise will reject with same reason.
 *
 * alias for calling:
 * ```kotlin
 * map(promises) { it }
 * ```
 *
 * @param[promises] List of promises.
 * @since 1.0
 * @return[PromiseInterface]
 */
fun all(promises: List<Any?>): PromiseInterface = map(promises) { it }

/**
 * Maps the given [promises] with [mapper].
 * Note: If any of the promise fails in the given [promises], the returning promise will reject with same reason.
 *
 * @param[promises] List of promises.
 * @since 1.0
 * @return[PromiseInterface]
 */
fun map(promises: List<Any?>, mapper: (Any?) -> Any?): PromiseInterface
{
	if (promises.isEmpty()) return resolve(promises)

	return Promise({ resolveCallback, rejectCallback ->
		val resolvedValues = mutableListOf<Any?>()
		var toResolve = promises.count()

		for (promise in promises)
		{
			resolve(promise)
				.then(mapper)
				.done({
					resolvedValues.add(it)

					if (0 == --toResolve)
					{
						resolveCallback(resolvedValues)
					}
				}, rejectCallback)
		}
	})
}