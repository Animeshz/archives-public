package lichi.brave

import net.dv8tion.jda.api.entities.SelfUser
import net.dv8tion.jda.api.events.ReadyEvent
import net.dv8tion.jda.api.events.message.MessageReceivedEvent
import net.dv8tion.jda.api.hooks.ListenerAdapter

/**
 * Handles events received via JDA
 */
class JDAEventHandler : ListenerAdapter()
{
	override fun onReady(event: ReadyEvent?)
	{
		val me: SelfUser? = event?.jda?.selfUser
		if (me != null)
		{
			println("Successfully logged in as ${me.name}#${me.discriminator} created on ${me.timeCreated}")
		}
	}

	override fun onMessageReceived(event: MessageReceivedEvent?)
	{
		val message = event?.message
		if (message != null)
		{
			Resources.commandDispatcher.handleMessage(message)
		}
	}
}
