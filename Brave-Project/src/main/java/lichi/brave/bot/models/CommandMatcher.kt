package lichi.brave.bot.models

import lichi.brave.bot.Client
import net.dv8tion.jda.api.entities.Message
import java.util.regex.Matcher
import java.util.regex.Pattern

class CommandMatcher(val client: Client, val message: Message, val pattern: Pattern, var commandIndex: Int = 1)
{
	var found: Boolean = false
	var commands: List<Command>? = null //set properties non settable by external sources
	var command: Command? = null
	var argString: String? = null

	init
	{
		match()
	}

	private fun match()
	{
		val match: Matcher = pattern.matcher(message.contentRaw)
		if (match.matches() || match.find())
		{
			var matchLength = 0
			for (i in 1..match.groupCount()) matchLength += match.group(i).length

			val commands = client.commandRegistry.findCommands(match.group(commandIndex), true)
			val commandsCount = commands.count()

			when (commandsCount)
			{
				0 ->
				{
					if (commandIndex != 1 && message.guild == null)
					{
						commandIndex = 1
						match()
					}
				}

				1 ->
				{
					found = true
					command = commands.first()
					argString = message.contentRaw.substring(matchLength)
				}

				else ->
				{
					found = true
					this.commands = commands
				}
			}
		}
	}
}
