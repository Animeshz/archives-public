package lichi.brave.bot.models

import net.dv8tion.jda.api.JDA

class Argument(val jda: JDA, val arg: Map<String, Any>)
{
	fun pull()
	{

	}

	fun parse(arg: String)
	{
		//convert the string object to specified type in arg property
	}

	fun validate(arg: String)
	{}
}
