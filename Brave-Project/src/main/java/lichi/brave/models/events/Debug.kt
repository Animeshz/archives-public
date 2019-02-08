package lichi.brave.models.events

import lichi.brave.models.EventEmitter

data class Debug(val message: String)
{
	companion object : EventEmitter<Debug>()

	fun emit() = Companion.emit(this)
}
