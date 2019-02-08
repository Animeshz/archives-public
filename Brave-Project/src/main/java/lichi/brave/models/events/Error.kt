package lichi.brave.models.events

import lichi.brave.models.EventEmitter

data class Error(val error: Throwable)
{
	companion object: EventEmitter<Error>()

	fun emit() = Companion.emit(this)
}
