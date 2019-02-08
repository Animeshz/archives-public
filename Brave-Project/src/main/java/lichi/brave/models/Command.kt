package lichi.brave.models

import lichi.brave.Resources
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.Permission
import net.dv8tion.jda.api.entities.ChannelType
import net.dv8tion.jda.api.entities.Message
import java.util.*

abstract class Command(val jda: JDA, info: Map<String, Any>)
{
	val name: String = info["name"] as String
	val aliases: List<String> = if(info["aliases"] != null) info["aliases"] as List<String> else listOf()
	val group: String? = info["group"] as String?
	val description: String = info["description"] as String
	val details: String? = info["details"] as String?
	val examples: List<String>? = info["examples"] as List<String>?
	val guildOnly: Boolean = info["guildOnly"] as Boolean? ?: false
	val ownerOnly: Boolean = info["ownerOnly"] as Boolean? ?: false
	val userPermissions: EnumSet<Permission>? = info["userPermissions"] as EnumSet<Permission>?
	val throttling: Map<String, Int>? = info["throttling"] as Map<String, Int>?
	val args: List<String>? = info["args"] as List<String>?
	val hidden: Boolean = info["hidden"] as Boolean? ?: false

	fun hasPermission(message: Message): Boolean
	{
		if (!ownerOnly && userPermissions == null) return true
		if (ownerOnly && !Resources.configuration.isOwner(message.author)) return false

		if (message.channel.type == ChannelType.TEXT && userPermissions != null)
		{
			val perms: EnumSet<Permission> = message.member.permissions
			if (!perms.containsAll(userPermissions)) return false
		}
		return true
	}

//	fun missingPermissions(message: Message): EnumSet<Permission>
//	{
//		if (!hasPermission(message))
//		{
//			val perms: EnumSet<Permission> = message.member.permissions
//			for (perm in userPermissions.iterator())
//			{
//				if (perm !in perms)
//			}
//		} else
//		{
//			return EnumSet.noneOf(Permission.class)
//		}
//	}

//	fun isEnabledIn(guild: Guild)
//	{
//
//	}
//
//	fun isEnabledIn(guild: Int)
//	{
//
//	}
//
//	fun isEnabledIn(guild: String)
//	{
//
//	}

	abstract fun run(message: Message?)
}