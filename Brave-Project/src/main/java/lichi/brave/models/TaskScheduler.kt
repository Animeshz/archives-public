package lichi.brave.models

import java.util.*
import kotlin.concurrent.schedule

class TaskScheduler
{
	private val timer: Timer = Timer(false)

	fun schedule(time: Long, task: TimerTask): TimerTask
	{
		timer.schedule(task, time)
		return task
	}

	fun schedule(time: Long, task: TimerTask.() -> Unit)
	{
		timer.schedule(time, task)
	}
}
