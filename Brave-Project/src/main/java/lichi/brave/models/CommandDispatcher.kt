package lichi.brave.models

import lichi.brave.Resources
import lichi.brave.models.events.Command.Run
import lichi.brave.models.events.Command.Blocked
import lichi.brave.models.events.Command.Error
import lichi.brave.models.events.Debug
import net.dv8tion.jda.api.EmbedBuilder
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.entities.Message
import net.dv8tion.jda.api.entities.SelfUser
import net.dv8tion.jda.api.entities.TextChannel
import java.awt.Color
import java.util.regex.Matcher
import java.util.regex.Pattern

class CommandDispatcher(val jda: JDA)
{
	private val awaiting: MutableList<String> = mutableListOf()

	private val commandPatterns: MutableMap<String, Pattern> = mutableMapOf()
	private lateinit var globalCommandPattern: Pattern

	private val inhibitors: MutableList<Inhibitor> = mutableListOf()

	/**
	 * Adds a inhibitor to message handling.
	 *
	 * It is intended to return null. If it returns a string,
	 * then the command will be blocked, user will be
	 * notified that it is blocked and Command.Blocked
	 * event will be triggered
	 */
	fun addInhibitor(inhibitor: Inhibitor): CommandDispatcher
	{
		if (!inhibitors.contains(inhibitor)) inhibitors.add(inhibitor)
		return this
	}

	/**
	 * Creates a regular expression to match the command prefix and name in a message.
	 * Returns string of pattern made by given prefix
	 */
	private fun buildCommandPattern(prefix: String? = null): Pattern
	{
		if (prefix == null) return buildGlobalCommandPattern()

		val me: SelfUser = jda.selfUser
		val escapedPrefix: String = Pattern.quote(prefix)
		val pattern: Pattern = Pattern.compile("(?iu)^(<@!?${me.id}>\\s+(?:$escapedPrefix\\s*)?|$escapedPrefix\\s*)([^\\s]+)")

		commandPatterns[prefix] = pattern
		Debug("Built command pattern for prefix $prefix: ${pattern.pattern()}").emit()

		return pattern
	}

	/**
	 * Creates a regular expression to match the command in a message globally (without prefix).
	 * Returns string of pattern
	 */
	private fun buildGlobalCommandPattern(): Pattern
	{
		val me: SelfUser = jda.selfUser
		val pattern: Pattern = Pattern.compile("(?iu)^(<@!?${me.id}>\\s+)([^\\s]+)")

		globalCommandPattern = pattern
		Debug("Built global command pattern: ${pattern.pattern()}").emit()

		return pattern
	}

	/**
	 * Handles an incoming message.
	 */
	fun handleMessage(message: Message, oldMessage: Message? = null)
	{
		if (!shouldHandleMessage(message, oldMessage)) return

		val context = parseCommand(message)
		val command: Command? = context?.get("command") as Command?
		if (command != null)
		{
			val args: String? = context?.get("args") as String?
			val inhibited: String? = inhibit(message)
			if (inhibited == null)
			{
				if (command.isEnabledIn(message.guild))
				{
					run(command, message, args)
				} else
				{
					message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription("Command is disabled in this server").build()).queue()
				}
			} else
			{
				message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription("CommandBlocked: $inhibited").build()).queue()
				Blocked(command, message, args, inhibited).emit()
			}
		}
	}

	/**
	 * Matches command from registered commands.
	 *
	 * Returns a map of command to Command and args to string from where args are started
	 */
	private fun matchCommand(message: Message, pattern: Pattern, commandIndex: Int = 1, prefix: String? = null): Map<String, Any>?
	{
		val match: Matcher = pattern.matcher(message.contentRaw)
		if (match.matches() || match.find())
		{
			var matchLength = 0
			for (i in 1..match.groupCount()) matchLength += match.group(i).length

			val commands = Resources.commandRegistry.findCommands(match.group(commandIndex), true)
			val commandsCount = commands.count()
			when (commandsCount)
			{
				0 -> if (Resources.configuration.unknownCommandResponse) message.channel.sendMessage(EmbedBuilder().setDescription("Unknown command, use" + (prefix ?: "" + "help")).build()).queue()
				1 -> return mapOf("command" to commands.first(), "args" to message.contentRaw.substring(matchLength))
			}
		}

		return null
	}

	/**
	 * Starts inhibiting message
	 */
	private fun inhibit(message: Message): String?
	{
		for (inhibitor in inhibitors)
		{
			val inhibited: String? = inhibitor.run(message)
			if (inhibited != null) return inhibited
		}
		return null
	}

	/**
	 * Parses command and generates pattern if necessary
	 */
	private fun parseCommand(message: Message): Map<String, Any>?
	{
		val prefix: String? = Resources.configuration.getGuildPrefix(message.guild)

		if (prefix == null && !::globalCommandPattern.isInitialized) buildCommandPattern()
		if (prefix != null && commandPatterns[prefix] == null) buildCommandPattern(prefix)
		val pattern: Pattern = if (prefix == null) globalCommandPattern else commandPatterns[prefix]!!

		var cmd: Map<String, Any>? = matchCommand(message, pattern, 2, prefix)
		if (cmd == null && message.guild == null) cmd = matchCommand(message, Pattern.compile("(?i)^([^\\s]+)"))

		return cmd
	}

	/**
	 * Removes inhibitor from registered inhibitors
	 */
	fun removeInhibitor(inhibitor: Inhibitor): CommandDispatcher
	{
		if (inhibitors.contains(inhibitor)) inhibitors.remove(inhibitor)
		return this
	}

	/**
	 * What you expect this to do?
	 */
	private fun run(command: Command, message: Message, args: String?)
	{
		if (command.guildOnly && message.guild == null)
		{
			Blocked(command, message, args, "Tried to run guild only command outside guild").emit()
			message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription("The `${command.name}` command must be used in a server.").build()).queue()
		}

		if (command.nsfw && !message.textChannel.isNSFW)
		{
			Blocked(command, message, args, "Tried to run nsfw command in non-nsfw channel").emit()
			message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription("The `${command.name}` command must be used in a NSFW channel.").build()).queue()
		}

		val missingPerms = command.checkPermission(message)
		if (missingPerms != null)
		{
			Blocked(command, message, args, "Tried to run command without required permission").emit()
			message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription(missingPerms).build()).queue()
		}

		//arg collector && filter output here

		Run(command, message, args).emit()
		command.run(message, mapOf())
	}

	/**
	 * States should we handle the command or not by returning Boolean value
	 */
	private fun shouldHandleMessage(message: Message, oldMessage: Message?): Boolean
	{
		val me: SelfUser = jda.selfUser
		if (message.author.isBot || message.author.id === me.id) return false

		if (message.guild != null && !message.guild.isAvailable) return false

		// Ignore messages from users that the bot is already waiting for input from
		if (awaiting.contains(message.author.id + message.channel.id)) return false

		if (oldMessage != null && message.contentRaw == oldMessage.contentRaw) return false

		return true
	}
}
