package lichi.brave.util.events

import lichi.brave.util.EventEmitter

data class Debug(val message: String)
{
	companion object : EventEmitter<Debug>()

	fun emit() = Companion.emit(this)
}
