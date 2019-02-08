package lichi.brave.models

import lichi.brave.util.ClassHelper
import net.dv8tion.jda.api.JDA

class CommandRegistry(private val jda: JDA)
{
	private val commands: MutableList<Command> = mutableListOf()

	fun findCommands(search: String, exact: Boolean = false): List<Command>
	{
		val searchString = search.toLowerCase()
		val matches: MutableList<Command> = mutableListOf()

		for (cmd: Command in commands)
		{
			if (exact)
			{
				if (cmd.name == searchString || cmd.aliases.contains(searchString)) matches.add(cmd)
			} else
			{
				if(cmd.name.contains(searchString) || cmd.aliases.filter { it.contains(searchString) }.count() >= 1) matches.add(cmd)
			}
		}

		if(!exact)
		{
			//check if we get exact name as in search, if yes return it alone
			for (cmd: Command in matches)
			{
				if(cmd.name == searchString || cmd.aliases.contains(searchString)) return listOf(cmd)
			}
		}

		return matches
	}

	fun registerCommandsIn(packageName: String)
	{
		val commandClasses: MutableList<Command> = mutableListOf()

		for (cls: Class<*> in ClassHelper.getClasses(packageName))
		{
			val obj: Any = cls.getDeclaredConstructor(JDA::class.java).newInstance(jda)
			if (obj is Command) commandClasses.add(obj)
		}

		commands.addAll(commandClasses)
	}
}