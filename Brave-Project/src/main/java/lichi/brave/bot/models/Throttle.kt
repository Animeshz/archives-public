package lichi.brave.bot.models

import java.util.TimerTask

data class Throttle(var start: Int, var usages: Int)
{
	var associatedTask: TimerTask? = null

	fun incrementUsage(): Throttle
	{
		usages++

		return this
	}

	fun resetUsage(): Throttle
	{
		usages = 0

		return this
	}

	fun updateStart(time: Int): Throttle
	{
		start = time

		return this
	}
}
