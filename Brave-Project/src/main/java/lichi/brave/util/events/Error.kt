package lichi.brave.util.events

import lichi.brave.util.EventEmitter

data class Error(val error: Throwable)
{
	companion object: EventEmitter<Error>()

	fun emit() = Companion.emit(this)
}
