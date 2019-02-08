package lichi.brave.models

import lichi.brave.Resources
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.Permission
import net.dv8tion.jda.api.entities.ChannelType
import net.dv8tion.jda.api.entities.Guild
import net.dv8tion.jda.api.entities.Message
import java.util.*

abstract class Command(val jda: JDA, info: Map<String, Any>)
{
	val name: String = info["name"] as String
	val aliases: List<String> = if (info["aliases"] != null) info["aliases"] as List<String> else listOf()
	val group: String? = info["group"] as String?
	val description: String = info["description"] as String
	val details: String? = info["details"] as String?
	val examples: List<String>? = info["examples"] as List<String>?
	val guildOnly: Boolean = info["guildOnly"] as Boolean? ?: false
	val globalEnabled: Boolean = info["globalEnabled"] as Boolean? ?: true
	val guildEnabled: Map<String, Boolean> = mapOf()
	val ownerOnly: Boolean = info["ownerOnly"] as Boolean? ?: false
	val userPermissions: EnumSet<Permission>? = info["userPermissions"] as EnumSet<Permission>?
	val throttling: Map<String, Int>? = info["throttling"] as Map<String, Int>?
	val args: List<String>? = info["args"] as List<String>?
	val nsfw: Boolean = info["nsfw"] as Boolean? ?: false
	val hidden: Boolean = info["hidden"] as Boolean? ?: false

	fun checkPermission(message: Message): String?
	{
		if (!ownerOnly && userPermissions == null) return null
		if (ownerOnly && !Resources.configuration.isOwner(message.author)) return "This command requires you to be bot's owner"

		if (message.channel.type == ChannelType.TEXT && userPermissions != null)
		{
			val perms: EnumSet<Permission> = message.member.permissions
			if (!perms.containsAll(userPermissions)) {
				val missingPermissions = EnumSet.copyOf(userPermissions)
				missingPermissions.removeAll(perms)
				return "This command requires you to have the following permissions: $missingPermissions"
			}
		}
		return null
	}

	fun isEnabledIn(): Boolean
	{
		return globalEnabled
	}

	fun isEnabledIn(guild: Guild): Boolean
	{
		return if (guildEnabled[guild.id] == null) globalEnabled else guildEnabled[guild.id] == true
	}

	fun isEnabledIn(id: Long): Boolean
	{
		val guild = jda.getGuildById(id)
		return if (guildEnabled[guild.id] == null) globalEnabled else guildEnabled[guild.id] == true
	}

	fun isEnabledIn(id: String): Boolean
	{
		val guild = jda.getGuildById(id)
		return if (guildEnabled[guild.id] == null) globalEnabled else guildEnabled[guild.id] == true
	}

	abstract fun run(message: Message?)
}
