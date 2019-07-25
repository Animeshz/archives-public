package com.animeshz.promise

import kotlin.reflect.KClass

/**
 * A common interface for all the Promises.
 *
 * @since 1.0
 */
interface PromiseInterface
{
	/**
	 * Shows the state of promise.
	 *
	 * @see PromiseState
	 *
	 * @since 1.0
	 */
	val state: PromiseState

	/**
	 * [onFulfilledOrRejected] executes in order as attached regardless the promise fails or get resolved. It is just like always call, but the original value of promise is conserved and is handover to next call of consumer(then/done/otherwise).
	 *
	 * @param[onFulfilledOrRejected] A lambda which you want execute.
	 * @since 1.0
	 */
	fun always(onFulfilledOrRejected: () -> Unit): PromiseInterface

	/**
	 * Cancels the promise.
	 *
	 * @since 1.0
	 */
	fun cancel()

	/**
	 * Suppress everything, complete silence. i.e. you don't want to use the value of promise or handle the exception. Calling this will break the chain, however any variable containing promise will still be able to use the promise.
	 *
	 * @since 1.0
	 */
	fun done()

	/**
	 * Attaches a last consumer for resolve and then suppress the promise, break the chain.
	 *
	 * Note: Since the promise chain break, if the consumer throws an exception the exception, the exception will threw by this function
	 *
	 * @param[onFulfilled] Consumer for resolve value of promise
	 * @since 1.0
	 */
	fun done(onFulfilled: (Any?) -> Any?)

	/**
	 * Attaches a last consumer for both resolve, reject and then suppress the promise, break the chain.
	 *
	 * Note: Since the promise chain break, if the consumer throws an exception the exception, the exception will threw by this function
	 *
	 * @param[onFulfilled] Consumer for resolve value of promise
	 * @param[onRejected] Consumer for rejection (Error/Exception caused in resolving) of promise
	 * @since 1.0
	 */
	fun done(onFulfilled: ((Any?) -> Any?)? = null, onRejected: (Throwable) -> Any?)

	/**
	 * Returns itself
	 * @since 1.0
	 */
	fun then(): PromiseInterface

	/**
	 * Attaches a consumer for resolve and continues to chain the promise by the result/error we are provided by the consumer (onFulfilled).
	 *
	 * @param[onFulfilled] Consumer for resolve value of promise
	 * @since 1.0
	 */
	fun then(onFulfilled: (Any?) -> Any?): PromiseInterface

	/**
	 * Attaches a consumer for both resolve, reject and continues to chain the promise by the result/error we are provided by either of the consumer (onFulfilled/onRejected).
	 *
	 * @param[onFulfilled] Consumer for resolve value of promise
	 * @param[onRejected] Consumer for rejection (Error/Exception caused in resolving) of promise
	 * @since 1.0
	 */
	fun then(onFulfilled: ((Any?) -> Any?)? = null, onRejected: (Throwable) -> Any?): PromiseInterface

	/**
	 * Attaches a consumer for rejection of specific Throwable/Exception and continues to chain the promise by the result/error we are provided by consumer (onRejected).
	 * If the Exception was not matched or was not subclass of given klass, then the Exception will propagate to the next consumer (otherwise/then/done).
	 *
	 * Note: There is no reason to call this after casual otherwise as that will catch all the exception
	 *
	 * Example:
	 * ```kotlin
	 * promise.otherwise(IOException::class) {
	 *     // catch IOException if it occurs
	 * }.otherwise(IllegalArgumentException::class) {
	 *     // catch IllegalArgumentException if it occurs
	 *     // note if you throw an exception here, it will propagate to the next call of consumer (otherwise/then/done)
	 * }.otherwise {
	 *     // catch all kind of other exceptions that wasn't been caught in above handlers
	 * }
	 * ```
	 *
	 * @param[klass] KClass object of type of exception you want to catch, you can get KClass by adding `::class` to the class statically.
	 * @param[onRejected] Consumer for rejection (Error/Exception caused in resolving) of promise
	 * @since 1.0
	 */
	fun <T : Throwable> otherwise(klass: KClass<T>, onRejected: (T) -> Any?): PromiseInterface

	/**
	 * Attaches a consumer for reject and continues to chain the promise by the result/error we are provided by consumer (onRejected).
	 * Alias for then(null, onRejected)
	 *
	 * @param[onRejected] Consumer for rejection (Error/Exception caused in resolving) of promise
	 * @since 1.0
	 */
	fun otherwise(onRejected: (Throwable) -> Any?): PromiseInterface
}