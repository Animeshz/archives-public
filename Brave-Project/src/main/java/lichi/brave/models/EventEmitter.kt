package lichi.brave.models

open class EventEmitter<T>
{
	private val handlers = mutableListOf<(T) -> Unit>()

	infix fun on(handler: (T) -> Unit) {
		handlers.add(handler)
	}

	fun emit(event: T) {
		for (subscriber in handlers) {
			subscriber(event)
		}
	}
}
