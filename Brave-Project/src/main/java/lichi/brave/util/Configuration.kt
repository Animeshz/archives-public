package lichi.brave.util

import kotlinx.serialization.Optional
import kotlinx.serialization.Serializable
import lichi.brave.exception.MissingArgumentException
import net.dv8tion.jda.api.entities.Guild
import net.dv8tion.jda.api.entities.User
import java.lang.IllegalArgumentException

@Serializable
data class Configuration(val token: String, val owners: List<String>, val database: Map<String, String>, @Optional val commandPrefix: String? = null, @Optional val disableEveryone: Boolean = true)
{
	init
	{
		if (database["host"] == null || database["username"] == null || database["password"] == null || database["database"] == null) throw MissingArgumentException("Database configuration is not set")
	}
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

	fun reload(database: DatabaseProvider)
	{
		//reload the list of configuration from database if edited
	}
}
