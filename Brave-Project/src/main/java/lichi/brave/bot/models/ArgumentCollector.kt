package lichi.brave.bot.models

import lichi.brave.bot.Client
import net.dv8tion.jda.api.entities.Message

class ArgumentCollector(val client: Client, val args: List<Map<String, Any>>)
{
	fun collect(message: Message, args: String): List<String>
	{
		client.commandDispatcher.setAwaiting(message)

		return listOf()
	}
}
