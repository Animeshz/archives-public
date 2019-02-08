package lichi.brave

import lichi.brave.models.CommandDispatcher
import lichi.brave.models.CommandRegistry
import lichi.brave.models.events.Debug
import lichi.brave.models.events.Error
import net.dv8tion.jda.api.JDABuilder
import java.io.PrintWriter
import java.io.BufferedWriter
import java.io.FileWriter


fun main()
{
	Debug on {
		println(it.message)
	}

	var counter = 1
	Error on {
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

	Resources.jda = JDABuilder("NTQwMTg5OTg4NjM4MTYzMDA0.Dz3RuQ.GutjtH13S920mHGYjM3X098E_08").addEventListeners(EventHandler()).build()
	Resources.commandRegistry = CommandRegistry(Resources.jda)
	Resources.commandDispatcher = CommandDispatcher(Resources.jda)

	Resources.commandRegistry.registerCommandsIn("lichi.brave.commands.static")
}
