package lichi.brave.util

import java.io.File

class DataHelper
{
	companion object
	{
		/**
		 * Returns full content of file as String
		 */
		fun fileToString(fileName: String): String = File(fileName).readText(Charsets.UTF_8)
	}
}
