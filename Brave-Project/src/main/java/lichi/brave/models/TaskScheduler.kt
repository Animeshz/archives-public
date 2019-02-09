package lichi.brave.models

import java.util.*
import kotlin.concurrent.schedule

class TaskScheduler
{
	val timer: Timer = Timer(false)

	fun schedule(time: Long, task: TimerTask.() -> Unit)
	{
		timer.schedule(time, task)
	}
}
