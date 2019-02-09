package lichi.brave.models

data class Throttle(var start: Int, var usages: Int)
{
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
