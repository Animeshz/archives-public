package lichi.brave.models

import lichi.brave.Resources
import net.dv8tion.jda.api.EmbedBuilder
import net.dv8tion.jda.api.JDA
import net.dv8tion.jda.api.entities.Message
import net.dv8tion.jda.api.entities.MessageEmbed
import net.dv8tion.jda.api.entities.SelfUser
import java.time.Instant
import java.time.temporal.TemporalAccessor
import java.util.*
import java.util.regex.Matcher
import java.util.regex.Pattern

class CommandDispatcher(val jda: JDA)
{
	private val awaiting: MutableList<String> = mutableListOf()

	private val commandPatterns: MutableMap<String, Pattern> = mutableMapOf()
	private lateinit var globalCommandPattern: Pattern

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

		return pattern
	}

	private fun buildGlobalCommandPattern(): Pattern
	{
		val me: SelfUser = jda.selfUser
		val pattern: Pattern = Pattern.compile("(?iu)^(<@!?${me.id}>\\s+)([^\\s]+)")
		globalCommandPattern = pattern

		return pattern
	}

	/**
	 * Handles an incoming message.
	 */
	fun handleMessage(message: Message, oldMessage: Message? = null)
	{
		//		try
		//		{
		if (!shouldHandleMessage(message, oldMessage)) return

		val command: Command? = parseCommand(message)
				println(command)

		//			val runnable: Boolean = inhibit(message)
		//				if ($cmdMessage->command) {
		//				if ($cmdMessage->command->isEnabledIn($message->guild)) {
		//				$cmdMessage->run()->done(function ($responses = null) use ($message, $oldMessage, $cmdMessage, $resolve) {
		//				if ($responses !== null && !\is_array($responses)) {
		//				$responses = array($responses);
		//			}
		//
		//				$cmdMessage->finalize($responses);
		//				$this->cacheCommandMessage($message, $oldMessage, $cmdMessage, $responses);
		//				$resolve();
		//			});
		//			} else {
		//				$message->reply('The command `'.$cmdMessage->command->name.'` is disabled.')->done(function ($response) use ($message, $oldMessage, $cmdMessage, $resolve) {
		//				$responses = array($response);
		//				$cmdMessage->finalize($responses);
		//
		//				$this->cacheCommandMessage($message, $oldMessage, $cmdMessage, $responses);
		//				$resolve();
		//			});
		//			}
		//			} else {
		//				$this->client->emit('unknownCommand', $cmdMessage);
		//				if (((bool) $this->client->getOption('unknownCommandResponse', true))) {
		//				$message->reply('Unknown command. Use '.\CharlotteDunois\Livia\Commands\Command::anyUsage('help').'.')->done(function ($response) use ($message, $oldMessage, $cmdMessage, $resolve) {
		//				$responses = array($response);
		//				$cmdMessage->finalize($responses);
		//
		//				$this->cacheCommandMessage($message, $oldMessage, $cmdMessage, $responses);
		//				$resolve();
		//			});
		//			}
		//			}
		//			}, function ($inhibited) use ($message, $oldMessage, $cmdMessage, $resolve) {
		//				if (!\ is_array ($inhibited)) {
		//				$inhibited = array($inhibited, null);
		//			}
		//
		//				$this->client->emit('commandBlocked', $cmdMessage, $inhibited[0]);
		//
		//				if (!($inhibited[1] instanceof \React\Promise\PromiseInterface)) {
		//				$inhibited[1] = \ React \ Promise \ resolve ($inhibited[1]);
		//			}
		//
		//				$inhibited[1]->done(function ($responses) use ($message, $oldMessage, $cmdMessage, $resolve) {
		//				if ($responses !== null) {
		//				$responses = array($responses);
		//			}
		//
		//				$cmdMessage->finalize($responses);
		//				$this->cacheCommandMessage($message, $oldMessage, $cmdMessage, $responses);
		//				$resolve();
		//			});
		//			});
		//			} elseif ($oldCmdMessage) {
		//			$oldCmdMessage->finalize(null);
		//			if (!$this->client->getOption('nonCommandEditable')) {
		//			$this->results->delete($message->id);
		//		}
		//
		//			$this->cacheCommandMessage($message, $oldMessage, $cmdMessage, array());
		//			$resolve();
		//		}
		//		} catch (\Throwable $error) {
		//		$this->client->emit('error', $error);
		//		throw $error;
		//		}
	}

	private fun matchCommand(message: Message, pattern: Pattern, commandIndex: Int = 1): Command?
	{
		val match: Matcher = pattern.matcher(message.contentRaw)
		if (match.matches() || match.find())
		{
			val commands = Resources.commandRegistry.findCommands(match.group(commandIndex), true)
			val commandsCount = commands.count()
			when (commandsCount)
			{
				0 -> if (Resources.configuration.unknownCommandResponse) message.channel.sendMessage(EmbedBuilder().setDescription("Command not found for name: ${message.contentRaw}").build()).queue()
				1 -> return commands.first()
			}
		}

		return null
	}

	private fun inhibit(message: Message): Boolean
	{
		return true
	}

	private fun parseCommand(message: Message): Command?
	{
		val prefix: String? = Resources.configuration.getGuildPrefix(message.guild)

		if (prefix == null && !::globalCommandPattern.isInitialized) buildCommandPattern()
		if (prefix != null && commandPatterns[prefix] == null) buildCommandPattern(prefix)
		val pattern: Pattern = if (prefix == null) globalCommandPattern else commandPatterns[prefix]!!

		var cmd: Command? = matchCommand(message, pattern, 2)
		if (cmd == null && message.guild == null) cmd = matchCommand(message, Pattern.compile("(?i)^([^\\s]+)"))

		return cmd
	}

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