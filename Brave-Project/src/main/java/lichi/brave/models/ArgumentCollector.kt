package lichi.brave.models

import lichi.brave.Resources
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.entities.Message

class ArgumentCollector(val jda: JDA, val args: List<Map<String, Any>>)
{
	fun collect(message: Message, args: String): List<String>
	{
		Resources.commandDispatcher.setAwaiting(message)

		return listOf()
	}
}
