package lichi.brave

import lichi.brave.models.CommandDispatcher
import lichi.brave.models.CommandRegistry
import net.dv8tion.jda.api.JDABuilder

fun main()
{
	Resources.jda = JDABuilder("NTQwMTg5OTg4NjM4MTYzMDA0.Dz3RuQ.GutjtH13S920mHGYjM3X098E_08").addEventListeners(EventHandler()).build()
	Resources.commandRegistry = CommandRegistry(Resources.jda)
	Resources.commandDispatcher = CommandDispatcher(Resources.jda)

	Resources.commandRegistry.registerCommandsIn("lichi.brave.commands.static")
}