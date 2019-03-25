package lichi.brave

import kotlinx.serialization.json.Json
import lichi.brave.bot.Client
import lichi.brave.util.Configuration
import lichi.brave.util.DataHelper
import lichi.brave.util.DatabaseProvider
import lichi.brave.util.TaskScheduler
import lichi.brave.util.events.Command
import lichi.brave.util.events.Debug
import java.io.BufferedWriter
import java.io.FileWriter
import java.io.PrintWriter

/**
 * Initializes Configuration instance by config.json present in root directory
 */
val configuration: Configuration = Json.parse(Configuration.serializer(), DataHelper.fileToString("config.json"))

/**
 * Our database
 */
val database: DatabaseProvider = DatabaseProvider()

/**
 * TaskScheduler
 */
val taskScheduler: TaskScheduler = TaskScheduler()

fun main()
{
	configuration.reload(database)
	registerEventHandlers()

	//initiate our bot's client
	Client()
}

/**
 * Handles the events of our application that are emitted by EventEmitter interface
 */
fun registerEventHandlers()
{
	Command on {
		when (it)
		{
			is Command.Blocked -> Debug("Command ${it.command.name} blocked(${it.message.author.asTag}): ${it.reason}. Message: '${it.message}'").emit()
			is Command.Run -> Debug("Command ${it.command.name} ran by ${it.message.author.asTag}. Message: '${it.message}'")
			is Command.Cancelled -> Debug("Command ${it.command.name} cancelled, ran by by ${it.message.author.asTag}. Message: '${it.message}'")
			is Command.Error -> Error("An error occurred in Command ${it.command.name} ran by ${it.message.author.asTag}. Message: '${it.message}'")
		}
	}
	Debug on {
		//database fetch channel and send
		println(it.message)
	}

	var counter = 1
	lichi.brave.util.events.Error on {
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
