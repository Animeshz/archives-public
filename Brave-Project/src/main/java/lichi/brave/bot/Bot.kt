package lichi.brave.bot

import lichi.brave.JDAEventHandler
import lichi.brave.bot.models.CommandDispatcher
import lichi.brave.bot.models.CommandRegistry
import lichi.brave.bot.models.Inhibitor
import lichi.brave.configuration
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.JDABuilder
import net.dv8tion.jda.api.entities.Message

class Bot
{
	/**
	 * CommandDispatcher instance
	 */
	private var commandDispatcher: CommandDispatcher

	/**
	 * CommandRegistry instance
	 */
	private var commandRegistry: CommandRegistry

	/**
	 * What do you expect?
	 */
	var jda: JDA = JDABuilder(configuration.token).addEventListeners(JDAEventHandler()).build()

	init
	{
		commandRegistry = CommandRegistry(jda)
		commandDispatcher = CommandDispatcher(jda)

		commandRegistry.registerCommandsIn("lichi.brave.bot.commands.static")

		//temporary testing part
		commandDispatcher.addInhibitor(object : Inhibitor
		{
			override fun run(message: Message): String?
			{
				return null
			}
		})
	}
}
