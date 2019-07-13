package com.animeshz.promise

import java.io.InvalidObjectException
import kotlin.reflect.KClass
import kotlin.reflect.full.cast
import kotlin.reflect.full.isSubclassOf

class Promise private constructor(private var canceller: (() -> Any?)?) : PromiseInterface
{
	private var requiredCancelRequests: Int = 0
	var state: PromiseState = PromiseState.PENDING
		private set

	private val handlers = mutableListOf<(PromiseInterface) -> Unit>()
	private var result: PromiseInterface? = null

	constructor(resolver: (resolve: (Any?) -> Any?, reject: (Throwable) -> Any?) -> Any?, canceller: (() -> Any?)? = null) : this(canceller)
	{
		call(resolver)
	}

	override fun always(onFulfilledOrRejected: () -> Unit): PromiseInterface
	{
		return this.then({ value ->
			resolve(onFulfilledOrRejected()).then {
				value
			}
		}, { reason ->
			resolve(onFulfilledOrRejected()).then {
				RejectedPromise(reason)
			}
		})
	}

	private fun call(callback: () -> Any?)
	{
		callback()
	}

	private fun call(callback: (resolve: (Any?) -> Any?, reject: (Throwable) -> Any?) -> Any?)
	{
		callback(this::resolveCallback, this::rejectCallback)
	}

	override fun cancel()
	{
		val canceller = this.canceller
		this.canceller = null

		var parentCanceller: (() -> Unit)? = null
		val r = result

		if (r !== null)
		{
			val root = unwrap(r)
			if (root !is Promise || root.result !== null) return

			root.requiredCancelRequests--
			if (root.requiredCancelRequests <= 0) parentCanceller = root::cancel
		}

		if (canceller !== null) call(canceller)
		if (parentCanceller !== null) parentCanceller()
	}

	override fun done()
	{
		val r = result
		when
		{
			r !== null -> return r.done()
			else -> handlers.add { promise: PromiseInterface -> promise.done() }
		}
	}

	override fun done(onFulfilled: (Any?) -> Any?)
	{
		val r = result
		when
		{
			r !== null -> return r.done(onFulfilled)
			else -> handlers.add { promise: PromiseInterface -> promise.done(onFulfilled) }
		}
	}

	override fun done(onFulfilled: ((Any?) -> Any?)?, onRejected: (Throwable) -> Any?)
	{
		val res = result
		when
		{
			res !== null -> return res.done(onFulfilled, onRejected)
			else -> handlers.add { promise: PromiseInterface -> promise.done(onFulfilled, onRejected) }
		}
	}

	override fun <T : Throwable> otherwise(klass: KClass<T>, onRejected: (T) -> Any?): PromiseInterface
	{
		return this.then(null, { e ->
			if (e::class.isSubclassOf(klass))
				onRejected(klass.cast(e))
			else
				RejectedPromise(e)
		})
	}

	override fun otherwise(onRejected: (Any: Throwable) -> Any?): PromiseInterface
	{
		return this.then(null, {	e -> onRejected(e) })
	}

	private fun rejectCallback(reason: Throwable)
	{
		if (result !== null) return
		settle(reject(reason))
	}

	private fun resolveCallback(value: Any? = null)
	{
		if (result !== null) return
		settle(resolve(value))
	}

	private fun resolver(onFulfilled: (Any?) -> Any?): ((Any?) -> Any?, (Throwable) -> Any?) -> Unit
	{
		return { resolve, reject ->
			handlers.add { promise ->
				promise.then(onFulfilled).done(resolve, reject)
			}
		}
	}

	private fun resolver(onFulfilled: ((Any?) -> Any?)?, onRejected: (Throwable) -> Any?): ((Any?) -> Any?, (Throwable) -> Any?) -> Unit
	{
		return { resolve, reject ->
			val promise = { promise: PromiseInterface ->
				promise.then(onFulfilled, onRejected).done(resolve, reject)
			}
			handlers.add(promise)
		}
	}

	private fun settle(result: PromiseInterface)
	{
		val res = unwrap(result)
		val finalResult = if (res === this) RejectedPromise(InvalidObjectException("Cannot resolve a promise by itself")) else res

		if (finalResult is Promise) finalResult.requiredCancelRequests++ else canceller = null

		val handlers = this.handlers.toList()
		this.handlers.clear()

		this.result = finalResult
		for (handler in handlers) handler(finalResult)

		state = when (finalResult)
		{
			is RejectedPromise -> PromiseState.REJECTED
			else -> PromiseState.FULFILLED
		}
	}

	override fun then(): PromiseInterface
	{
		return this
	}

	override fun then(onFulfilled: (Any?) -> Any?): PromiseInterface
	{
		val res = result
		if (res !== null) return res.then(onFulfilled)

		if (canceller === null) return Promise(resolver(onFulfilled))

		requiredCancelRequests++
		return Promise(resolver(onFulfilled), {
			requiredCancelRequests--
			if (requiredCancelRequests <= 0) cancel()
		})
	}

	override fun then(onFulfilled: ((Any?) -> Any?)?, onRejected: (Throwable) -> Any?): PromiseInterface
	{
		val res = result

		if (res != null) return res.then(onFulfilled, onRejected)

		if (canceller === null) return Promise(resolver(onFulfilled, onRejected))

		requiredCancelRequests++

		return Promise(resolver(onFulfilled, onRejected), {
			requiredCancelRequests--
			if (requiredCancelRequests <= 0) cancel()
		})
	}

	private fun unwrap(value: PromiseInterface): PromiseInterface
	{
		var res: PromiseInterface = value

		while (res is Promise && res.result !== null)
		{
			res = res.result!!
		}
		return res
	}
}