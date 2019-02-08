package lichi.brave.commands.static

import lichi.brave.models.Command
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.Permission
import net.dv8tion.jda.api.entities.Message
import java.util.*

class AvatarT(jda: JDA): Command(jda, mapOf<String, Any>("name" to "avatart", "description" to "A simple command for ", "userPermissions" to EnumSet.of(Permission.MESSAGE_WRITE)))
{
	override fun run(message: Message?)
	{
		println(message?.contentDisplay)
		println(message?.contentRaw)
		println(message?.contentStripped)
	}
}