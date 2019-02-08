package lichi.brave

import lichi.brave.models.CommandDispatcher
import lichi.brave.models.CommandRegistry
import lichi.brave.models.Inhibitor
import lichi.brave.models.events.Command
import lichi.brave.models.events.Debug
import net.dv8tion.jda.api.JDABuilder
import net.dv8tion.jda.api.entities.Message
import java.io.BufferedWriter
import java.io.FileWriter
import java.io.PrintWriter

fun main()
{
	handleEvents()
	Resources.jda = JDABuilder("NTQwMTg5OTg4NjM4MTYzMDA0.Dz3RuQ.GutjtH13S920mHGYjM3X098E_08").addEventListeners(JDAEventHandler()).build()
	Resources.commandRegistry = CommandRegistry(Resources.jda)
	Resources.commandDispatcher = CommandDispatcher(Resources.jda)

	Resources.commandRegistry.registerCommandsIn("lichi.brave.commands.static")

	//temporary testing part
	Resources.commandDispatcher.addInhibitor(object : Inhibitor {
		override fun run(message: Message): String?
		{
			return null
		}
	})
}

fun handleEvents()
{
	Command on {
		when (it)
		{
			is Command.Blocked -> Debug("Command ${it.command.name} blocked(${it.message.author.asTag}): ${it.reason}").emit()
			is Command.Run -> Debug("Command ${it.command.name} ran by ${it.message.author.asTag}")
			is Command.Cancelled -> Debug("Command ${it.command.name} cancelled, ran by by ${it.message.author.asTag}")
			is Command.Error -> Error("An error occurred in Command ${it.command.name} ran by ${it.message.author.asTag} with `${it.args}` arguments")
		}
	}
	Debug on {
		//database fetch channel and send
		println(it.message)
	}

	var counter = 1
	lichi.brave.models.events.Error on {
		val fw = FileWriter("exception-logging.txt", true)
		val bw = BufferedWriter(fw)
		val pw = PrintWriter(bw)
		pw.println("$counter: ${it.error.message}")
		pw.println("StackTrace:")
		pw.println(it.error.stackTrace)
		pw.println()
		pw.println()
		pw.close()
		bw.close()
		fw.close()
		counter++
	}
}
