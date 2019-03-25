package lichi.brave.util.events

import lichi.brave.util.EventEmitter
import net.dv8tion.jda.api.entities.Message

sealed class Command
{
	companion object : EventEmitter<Command>()

	class Blocked(val command: lichi.brave.bot.models.Command, val message: Message, val reason: String? = null) : Command() {
		fun emit() = Companion.emit(this)
	}

	class Cancelled(val command: lichi.brave.bot.models.Command, val message: Message) : Command() {
		fun emit() = Companion.emit(this)
	}

	class Error(val command: lichi.brave.bot.models.Command, val message: Message, val reason: String? = null) : Command() {
		fun emit() = Companion.emit(this)
	}

	//List of any because under filtration or parse it might converted into some other type
	class Run(val command: lichi.brave.bot.models.Command, val message: Message, val args: List<Any>?) : Command() {
		fun emit() = Companion.emit(this)
	}
}
