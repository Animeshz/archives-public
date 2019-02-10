package lichi.brave.models

import lichi.brave.Resources
import lichi.brave.models.events.Debug
import lichi.brave.util.ClassHelper.Companion.checkItemsAre
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.Permission
import net.dv8tion.jda.api.entities.ChannelType
import net.dv8tion.jda.api.entities.Guild
import net.dv8tion.jda.api.entities.Message
import net.dv8tion.jda.api.entities.User
import java.util.*
import java.util.stream.Collectors
import kotlin.concurrent.timerTask

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
	val aliases: List<String>
	val group: String = info["group"] as String? ?: "default"
	val description: String = info["description"] as String? ?: ""
	val details: String? = info["details"] as String?
	val examples: List<String>
	val guildOnly: Boolean = info["guildOnly"] as Boolean? ?: false
	val globalEnabled: Boolean = info["globalEnabled"] as Boolean? ?: true
	val guildEnabled: Map<String, Boolean> = mapOf()
	val ownerOnly: Boolean = info["ownerOnly"] as Boolean? ?: false
	val userPermissions: EnumSet<Permission>?
	val throttling: Map<String, Int>?
	val throttles: MutableMap<String, Throttle> = mutableMapOf()
	val args: List<Map<String, Any>>?
	val nsfw: Boolean = info["nsfw"] as Boolean? ?: false
	val hidden: Boolean = info["hidden"] as Boolean? ?: false

	init
	{
		val errorMessage = "Wrong argument passed for %s of command $name"

		val tempAliases = if (info["aliases"] == null) null else info["aliases"] as? List<*> ?: throw IllegalStateException(errorMessage.format("aliases"))
		aliases = if (tempAliases == null) listOf() else tempAliases.checkItemsAre<String>() ?: throw IllegalStateException(errorMessage.format("aliases"))

		val tempExamples = if (info["examples"] == null) null else info["examples"] as? List<*> ?: throw IllegalStateException(errorMessage.format("examples"))
		examples = if (tempExamples == null) listOf() else tempExamples.checkItemsAre<String>() ?: throw IllegalArgumentException(errorMessage.format("examples"))

		val tempUserPermissions = if (info["userPermissions"] == null) null else info["userPermissions"] as? EnumSet<*> ?: throw IllegalStateException(errorMessage.format("userPermissions"))
		userPermissions = tempUserPermissions?.stream()?.filter { it is Permission }?.map { it as Permission }?.collect(Collectors.toCollection { EnumSet.noneOf(Permission::class.java) })

		val tempThrottling = if (info["throttling"] == null) null else info["throttling"] as? Map<*, *> ?: throw IllegalStateException(errorMessage.format("throttling"))
		val tempThrottling2 = tempThrottling?.filter { it.key is String && it.value is Int }?.mapKeys { it.key as String }?.mapValues { it.value as Int }
		if (tempThrottling2 != null && (tempThrottling2["usages"] == null || tempThrottling2["time"] == null)) throw IllegalStateException("Some parameter for throttling of command $name is missing")
		throttling = tempThrottling2

		val tempArgs = if (info["args"] == null) null else info["args"] as? List<*> ?: throw IllegalStateException(errorMessage.format("args"))
		val tempArgs2 = tempArgs?.asSequence()?.filter { it is Map<*, *> }?.map { it as Map<*, *> }?.filter { it.keys.all { key -> key is String } }?.map { it.mapKeys { it2 -> it2.key as String } }?.map { it.mapValues { it2 -> it2.value as Any } }?.toList()
		if (tempArgs2 != null && tempArgs2.all { it["name"] == null }) throw IllegalStateException("name is missing from args of command $name")
		args = tempArgs2
	}

	/**
	 * Checks if the user have access to use the command or not by a given message.
	 *
	 * If null is returned user can run the command.
	 * If returns a string, string is reason for not able to use command
	 */
	fun checkPermission(message: Message): String?
	{
		//		if (!ownerOnly && userPermissions == null) return null
		//		if (ownerOnly && !Resources.configuration.isOwner(message.author)) return "This command requires you to be bot's owner"
		//
		//		if (message.channel.type == ChannelType.TEXT && userPermissions != null)
		//		{
		//			val perms: EnumSet<Permission> = message.member.permissions
		//			if (!perms.containsAll(userPermissions))
		//			{
		//				val missingPermissions = EnumSet.copyOf(userPermissions)
		//				missingPermissions.removeAll(perms)
		//				return "This command requires you to have the following permissions: $missingPermissions"
		//			}
		//		}
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
			throttle.associatedTask = Resources.taskScheduler.schedule(deleteTime, timerTask { throttle.resetUsage() })
			Debug("Created throttle object for $userID its usage will be reset to 0 in ${deleteTime / 1000} seconds").emit()
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
		throttle.associatedTask?.cancel()
		throttle.associatedTask = null

		val deleteTime = (throttling!!.getValue("time") * 1000).toLong()
		Resources.taskScheduler.schedule(deleteTime) { throttle.resetUsage() }
		Debug("Throttle usage for $userID incremented to $throttle, will be reset in ${deleteTime / 1000} seconds").emit()

		return throttle
	}
}
