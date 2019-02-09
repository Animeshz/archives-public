package lichi.brave.models

import lichi.brave.Resources
import lichi.brave.models.events.Debug
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.Permission
import net.dv8tion.jda.api.entities.ChannelType
import net.dv8tion.jda.api.entities.Guild
import net.dv8tion.jda.api.entities.Message
import net.dv8tion.jda.api.entities.User
import java.util.*

/**
 * Represents a command.
 *
 * To create a command inherit this class with implementing
 * run() function, and pass info as a Map in the following manner:
 * ```
 * name: String
 * aliases: List<String> (optional)
 * group: String (optional, but recommended)
 * description: String (optional)
 * details: String (optional)
 * examples: List<String> (optional)
 * guildOnly: Boolean (defaults to false)
 * ownerOnly: Boolean (defaults to false)
 * userPermissions: EnumSet<Permission> (optional)
 * throttling: Map<String, Int> (optional)
 *     "usage" to Int (number of usage)
 *     "time" to Int (in sec)
 * args: List<String>
 * nsfw: Boolean
 * hidden: (defaults to false)
 * ```
 *
 * To register commands in a package call
 * CommandRegistry.registerCommandsIn(packageToFindForCommands)
 */
abstract class Command(val jda: JDA, info: Map<String, Any>)
{
	val name: String = info["name"] as String
	val aliases: List<String> = info["aliases"] as List<String>? ?: listOf()
	val group: String = info["group"] as String? ?: "default"
	val description: String = info["description"] as String? ?: ""
	val details: String? = info["details"] as String?
	val examples: List<String>? = info["examples"] as List<String>?
	val guildOnly: Boolean = info["guildOnly"] as Boolean? ?: false
	val globalEnabled: Boolean = info["globalEnabled"] as Boolean? ?: true
	val guildEnabled: Map<String, Boolean> = mapOf()
	val ownerOnly: Boolean = info["ownerOnly"] as Boolean? ?: false
	val userPermissions: EnumSet<Permission>? = info["userPermissions"] as EnumSet<Permission>?
	val throttling: Map<String, Int>? = info["throttling"] as Map<String, Int>?
	val throttles: MutableMap<String, Throttle> = mutableMapOf()
	val args: List<String>? = info["args"] as List<String>?
	val nsfw: Boolean = info["nsfw"] as Boolean? ?: false
	val hidden: Boolean = info["hidden"] as Boolean? ?: false

	/**
	 * Checks if the user have access to use the command or not by a given message.
	 *
	 * If null is returned user can run the command.
	 * If returns a string, string is reason for not able to use command
	 */
	fun checkPermission(message: Message): String?
	{
		if (!ownerOnly && userPermissions == null) return null
		if (ownerOnly && !Resources.configuration.isOwner(message.author)) return "This command requires you to be bot's owner"

		if (message.channel.type == ChannelType.TEXT && userPermissions != null)
		{
			val perms: EnumSet<Permission> = message.member.permissions
			if (!perms.containsAll(userPermissions))
			{
				val missingPermissions = EnumSet.copyOf(userPermissions)
				missingPermissions.removeAll(perms)
				return "This command requires you to have the following permissions: $missingPermissions"
			}
		}
		return null
	}

	/**
	 * Determine if command is enabled globally
	 */
	fun isEnabledIn(): Boolean
	{
		return globalEnabled
	}

	/**
	 * Determine if command is enabled in the given guild
	 */
	fun isEnabledIn(guild: Guild): Boolean
	{
		return if (guildEnabled[guild.id] == null) globalEnabled else guildEnabled[guild.id] == true
	}

	/**
	 * Determine if command is enabled in the given guild
	 */
	fun isEnabledIn(id: Long): Boolean
	{
		val guild = jda.getGuildById(id)
		return if (guildEnabled[guild.id] == null) globalEnabled else guildEnabled[guild.id] == true
	}

	/**
	 * Determine if command is enabled in the given guild
	 */
	fun isEnabledIn(id: String): Boolean
	{
		val guild = jda.getGuildById(id)
		return if (guildEnabled[guild.id] == null) globalEnabled else guildEnabled[guild.id] == true
	}

	/**
	 * Must be implemented in your command.
	 * Set of instruction that execute when command runs.
	 */
	abstract fun run(message: Message?, args: Map<String, String>)

	fun throttle(user: User): Throttle?
	{
		val userID = user.id
		return throttle(userID)
	}

	fun throttle(userID: String): Throttle?
	{
		if (throttling == null) return null

		if (!throttles.contains(userID))
		{
			val throttle = Throttle((System.currentTimeMillis() / 1000).toInt(), 0)
			throttles[userID] = throttle

			val deleteTime = (throttling.getValue("time") * 1000).toLong()
			Resources.taskScheduler.schedule(deleteTime) { throttle.resetUsage() }
			Debug("Created throttle object for $userID its usage will be reset to 0 in ${deleteTime/1000} seconds").emit()
		}

		return throttles[userID]
	}

	fun incrementThrottle(user: User): Throttle?
	{
		val userID = user.id
		return incrementThrottle(userID)
	}

	fun incrementThrottle(userID: String): Throttle?
	{
		val throttle = throttle(userID) ?: return null

		throttle.updateStart((System.currentTimeMillis() / 1000).toInt()).incrementUsage()

		val deleteTime = (throttling!!.getValue("time") * 1000).toLong()
		Resources.taskScheduler.schedule(deleteTime) { throttles.remove(userID) }
		Debug("Throttle for $userID incremented to $throttle, Throttle object will be deleted in ${deleteTime/1000} seconds").emit()

		return throttle
	}
}
