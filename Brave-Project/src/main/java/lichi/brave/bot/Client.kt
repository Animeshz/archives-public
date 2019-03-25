package lichi.brave.bot

import lichi.brave.JDAEventHandler
import lichi.brave.bot.models.CommandDispatcher
import lichi.brave.bot.models.CommandRegistry
import lichi.brave.bot.models.Inhibitor
import lichi.brave.configuration
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.JDABuilder
import net.dv8tion.jda.api.entities.Message

class Client
{
	/**
	 * CommandDispatcher instance
	 */
	val commandDispatcher: CommandDispatcher

	/**
	 * CommandRegistry instance
	 */
	val commandRegistry: CommandRegistry

	/**
	 * What do you expect?
	 */
	var jda: JDA = JDABuilder(configuration.token).addEventListeners(JDAEventHandler()).build()

	init
	{
		commandRegistry = CommandRegistry(this)
		commandDispatcher = CommandDispatcher(this)

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
