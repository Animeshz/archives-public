package lichi.brave.util

import java.io.File

class DataHelper
{
	companion object
	{
		fun fileToString(fileName: String): String = File(fileName).readText(Charsets.UTF_8)
	}
}
