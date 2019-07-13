package com.animeshz.promise

fun resolve(value: Any? = null): PromiseInterface = if (value is PromiseInterface) value else FulfilledPromise(
	value)

fun reject(reason: Throwable) = RejectedPromise(reason)

fun all(promises: List<Any?>): PromiseInterface = map(promises) { it }

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