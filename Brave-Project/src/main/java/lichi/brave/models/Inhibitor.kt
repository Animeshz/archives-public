package lichi.brave.models

import net.dv8tion.jda.api.entities.Message

interface Inhibitor
{
	fun run(message: Message): String?
}
