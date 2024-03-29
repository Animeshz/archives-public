package lichi.brave.bot.models

import lichi.brave.bot.Client
import lichi.brave.configuration
import lichi.brave.util.events.Command.Run
import lichi.brave.util.events.Command.Blocked
import lichi.brave.util.events.Debug
import net.dv8tion.jda.api.EmbedBuilder
import net.dv8tion.jda.api.entities.Message
import net.dv8tion.jda.api.entities.SelfUser
import java.awt.Color
import java.util.regex.Matcher
import java.util.regex.Pattern

class CommandDispatcher(private val client: Client)
{
	val awaiting: MutableList<String> = mutableListOf()

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

		val me: SelfUser = client.jda.selfUser
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
		val me: SelfUser = client.jda.selfUser
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

		val commandMatch = parseCommand(message)
		val command: Command = commandMatch.command ?: return
		val argString: String = commandMatch.argString!!
		val inhibited: String? = inhibit(message)

		if (inhibited != null)
		{
			Blocked(command, message, inhibited).emit()
			return message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription("CommandBlocked: $inhibited").build()).queue()
		}
		run(command, message, argString)
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
	private fun parseCommand(message: Message): CommandMatcher
	{
		val prefix: String? = configuration.getGuildPrefix(message.guild)

		if (prefix == null && !::globalCommandPattern.isInitialized) buildCommandPattern()
		if (prefix != null && commandPatterns[prefix] == null) buildCommandPattern(prefix)
		val pattern: Pattern = if (prefix == null) globalCommandPattern else commandPatterns[prefix]!!

		var cmd = CommandMatcher(client, message, pattern, 2)
		if (!cmd.found && message.guild == null) cmd = CommandMatcher(client, message, Pattern.compile("(?i)^([^\\s]+)"))

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
	private fun run(command: Command, message: Message, argString: String)
	{
		if (!command.isEnabledIn(message.guild)) {
			Blocked(command, message, "Command is disabled in guild").emit()
			return message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription("Command is disabled in this server").build()).queue()
		}

		if (command.guildOnly && message.guild == null)
		{
			Blocked(command, message, "Tried to run guild only command outside guild").emit()
			return message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription("The `${command.name}` command must be used in a server.").build()).queue()
		}

		if (command.nsfw && !message.textChannel.isNSFW)
		{
			Blocked(command, message, "Tried to run nsfw command in non-nsfw channel").emit()
			return message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription("The `${command.name}` command must be used in a NSFW channel.").build()).queue()
		}

		val missingPerms = command.checkPermission(message)
		if (missingPerms != null)
		{
			Blocked(command, message, "Tried to run command without required permission").emit()
			return message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription(missingPerms).build()).queue()
		}

		//throttle
		val throttle = command.throttle(message.author)
		if (throttle != null && throttle.usages + 1 > command.throttling!!.getValue("usages"))
		{
			val currentTime = (System.currentTimeMillis() / 1000).toInt()
			val remaining = throttle.start + command.throttling.getValue("time") - currentTime
			Blocked(command, message, "Throttle")

			return message.channel.sendMessage(EmbedBuilder().setColor(Color.RED).setDescription("You may not use the `${command.name}` command again for another $remaining seconds.").build()).queue()
		}

		//arg collector && filter output here
		val arguments = listOf<Any>()
		if (command.args != null)
		{
			if (command.args.count() > argString.split(' ').count())
			{
				command.argumentCollector!!.collect(message, argString)
			}
			//collect args and transform to map
			//arguments =
		}

		if (throttle != null) command.incrementThrottle(message.author)
		Run(command, message, arguments).emit()
		return command.run(message, mapOf())
	}

	/**
	 * States should we handle the command or not by returning Boolean value
	 */
	private fun shouldHandleMessage(message: Message, oldMessage: Message?): Boolean
	{
		val me: SelfUser = client.jda.selfUser
		if (message.author.isBot || message.author.id === me.id) return false

		if (message.guild != null && !message.guild.isAvailable) return false

		// Ignore messages from users that the client is already waiting for input from
		if (awaiting.contains(message.author.id + message.channel.id)) return false

		if (oldMessage != null && message.contentRaw == oldMessage.contentRaw) return false

		return true
	}

	fun setAwaiting(message: Message)
	{
		awaiting.add(message.author.id + message.channel.id)
	}

	fun removeAwaiting(message: Message)
	{
		val search = message.author.id + message.channel.id
		if (awaiting.contains(search)) awaiting.remove(search)
	}
}
