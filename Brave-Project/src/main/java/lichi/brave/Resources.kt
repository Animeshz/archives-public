package lichi.brave

import kotlinx.serialization.json.Json
import lichi.brave.models.CommandDispatcher
import lichi.brave.models.CommandRegistry
import lichi.brave.models.Configuration
import lichi.brave.util.DataHelper
import net.dv8tion.jda.api.JDA

class Resources
{
	companion object
	{
		/**
		 * CommandDispatcher instance
		 */
		lateinit var commandDispatcher: CommandDispatcher

		/**
		 * CommandRegistry instance
		 */
		lateinit var commandRegistry: CommandRegistry

		/**
		 * Initializes Configuration instance by config.json present in root directory
		 */
		val configuration: Configuration = Json.parse(Configuration.serializer(), DataHelper.fileToString("config.json"))

		/**
		 * What do you expect?
		 */
		lateinit var jda: JDA
	}
}
