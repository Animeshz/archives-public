package lichi.brave.models

import kotlinx.serialization.Optional
import kotlinx.serialization.Serializable
import net.dv8tion.jda.api.entities.Guild
import net.dv8tion.jda.api.entities.User

@Serializable
data class Configuration(val owners: List<String>, @Optional val commandPrefix: String? = null, @Optional val unknownCommandResponse: Boolean = true, @Optional val disableEveryone: Boolean = true)
{
	/**
	 * States if user given is a owner, returns a Boolean value
	 */
	fun isOwner(user: User): Boolean = user.id in owners

	/**
	 * States if user given is a owner, returns a Boolean value
	 */
	fun isOwner(userID: String): Boolean = userID in owners

	/**
	 * Fetch and return guildPrefix, if not found returns global prefix
	 */
	fun getGuildPrefix(guild: Guild): String?
	{
		//(using database) check if guild prefix is set
		//else
		return commandPrefix
	}
}
