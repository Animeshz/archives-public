package com.animeshz.promise

/**
 * Represents the state of promise
 *
 * @since 1.0
 */
enum class PromiseState(val state: Int)
{
	/**
	 * Pending state shows that promise has not completed, it may transition to Fulfilled state or Rejected state after the process has been completed.
	 *
	 * @since 1.0
	 */
	PENDING(0),

	/**
	 * Fulfilled state shows that the process has been successfully completed and the promise is fulfilled with the resulting value.
	 *
	 * @since 1.0
	 */
	FULFILLED(1),

	/**
	 * Rejected state shows that the process has been failed and the promise is rejected with a Throwable/Exception as a reason.
	 *
	 * @since 1.0
	 */
	REJECTED(2)
}