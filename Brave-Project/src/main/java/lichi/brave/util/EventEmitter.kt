package lichi.brave.util

open class EventEmitter<T>
{
	/**
	 * Registered handlers for events
	 */
	private val handlers = mutableListOf<(T) -> Unit>()

	/**
	 * register a handler for event
	 */
	infix fun on(handler: (T) -> Unit) {
		handlers.add(handler)
	}

	/**
	 * emits an event
	 */
	fun emit(event: T) {
		for (subscriber in handlers) {
			subscriber(event)
		}
	}
}
