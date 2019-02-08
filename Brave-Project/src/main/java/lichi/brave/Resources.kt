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
		lateinit var commandDispatcher: CommandDispatcher
		lateinit var commandRegistry: CommandRegistry
		val configuration: Configuration = Json.parse(Configuration.serializer(), DataHelper.fileToString("config.json"))
		lateinit var jda: JDA
	}
}
