package lichi.brave.util

import lichi.brave.configuration

class DatabaseProvider
{
	private val settings = mutableMapOf<String, Any>()

	init
	{
		val config = configuration.database

		//create connection with database and store the instance
	}

	private fun databaseRead()
	{
	}

	private fun databaseSync(key: String, value: Any)
	{
	}

	fun get(key: String) = settings[key]

	fun set(key: String, value: Boolean)
	{
		settings[key] = value
		databaseSync(key, value)
	}

	fun set(key: String, value: Double)
	{
		settings[key] = value
		databaseSync(key, value)
	}

	fun set(key: String, value: Float)
	{
		settings[key] = value
		databaseSync(key, value)
	}

	fun set(key: String, value: Int)
	{
		settings[key] = value
		databaseSync(key, value)
	}

	fun set(key: String, value: Long)
	{
		settings[key] = value
		databaseSync(key, value)
	}

	fun set(key: String, value: String)
	{
		settings[key] = value
		databaseSync(key, value)
	}

//	fun update(key: String, value: Boolean)
}
