package lichi.brave.bot.models

import net.dv8tion.jda.api.entities.Message

/**
 * Represents an Inhibitor, used for blocking the command
 */
interface Inhibitor
{
	/**
	 * Implement this method to use Inhibitor
	 */
	fun run(message: Message): String?
}
