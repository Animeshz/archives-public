package lichi.brave.util.events

import lichi.brave.util.EventEmitter
import net.dv8tion.jda.api.entities.Message

sealed class Command
{
	companion object : EventEmitter<Command>()

	class Blocked(val command: lichi.brave.bot.models.Command, val message: Message, val args: String? = null, val reason: String? = null) : Command() {
		fun emit() = Companion.emit(this)
	}

	class Cancelled(val command: lichi.brave.bot.models.Command, val message: Message, val args: String? = null) : Command() {
		fun emit() = Companion.emit(this)
	}

	class Error(val command: lichi.brave.bot.models.Command, val message: Message, val args: String? = null, val reason: String? = null) : Command() {
		fun emit() = Companion.emit(this)
	}

	class Run(val command: lichi.brave.bot.models.Command, val message: Message, val args: String?) : Command() {
		fun emit() = Companion.emit(this)
	}
}
