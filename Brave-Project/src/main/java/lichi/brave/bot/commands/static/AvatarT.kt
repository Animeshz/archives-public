package lichi.brave.bot.commands.static

import lichi.brave.bot.Client
import lichi.brave.bot.models.Command
import net.dv8tion.jda.api.Permission
import net.dv8tion.jda.api.entities.Message
import java.util.*

class AvatarT(client: Client): Command(client, mapOf<String, Any>("name" to "avatart", "description" to "A simple command for ", "userPermissions" to EnumSet.of(Permission.MESSAGE_WRITE)))
{
	override fun run(message: Message?, args: Map<String, String>)
	{
		println(message?.contentDisplay)
		println(message?.contentRaw)
		println(message?.contentStripped)
	}
}
