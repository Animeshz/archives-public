package lichi.brave.commands.static

import lichi.brave.models.Command
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.Permission
import net.dv8tion.jda.api.entities.Message
import java.util.*

class Avatar(jda: JDA): Command(jda, mapOf<String, Any>("name" to "avatar", "description" to "A simple command for ", "userPermissions" to EnumSet.of(Permission.MESSAGE_WRITE), "throttling" to mapOf("usages" to 2, "time" to 15), "args" to listOf(mapOf("name" to "henlo"))))
{
	override fun run(message: Message?, args: Map<String, String>)
	{
		println(message?.contentDisplay)
		println(message?.contentRaw)
		println(message?.contentStripped)
	}
}
