package lichi.brave.util

import java.io.File
import java.io.IOException
import java.security.Permission
import java.util.*

class ClassHelper
{
	companion object
	{
		/**
		 * Scans all classes accessible from the context class loader which belong to the given package and subpackages.
		 * Returns List of classes on the basis of packageName given
		 */
		@Throws(ClassNotFoundException::class, IOException::class)
		fun getClasses(packageName: String): List<Class<*>>
		{
			val classLoader = Thread.currentThread().contextClassLoader!!
			val path = packageName.replace('.', '/')
			val resources = classLoader.getResources(path)
			val dirs: MutableList<File> = mutableListOf()
			while (resources.hasMoreElements())
			{
				val resource = resources.nextElement()
				dirs.add(File(resource.file))
			}
			val classes: MutableList<Class<*>> = mutableListOf()
			for (directory in dirs)
			{
				classes.addAll(findClasses(directory, packageName))
			}
			return classes
		}

		/**
		 * Recursive method used to find all classes in a given directory and subdirs.
		 * Returns list of classes on the basis of directory and packageName
		 */
		@Throws(ClassNotFoundException::class)
		private fun findClasses(directory: File, packageName: String): List<Class<*>>
		{
			val classes: MutableList<Class<*>> = mutableListOf()
			if (!directory.exists()) return classes

			val files: Array<File> = directory.listFiles()
			for (file: File in files)
			{
				if (file.isDirectory)
				{
					assert(!file.name.contains("."))
					classes.addAll(findClasses(file, packageName + "." + file.name))
				} else if (file.name.endsWith(".class"))
				{
					classes.add(Class.forName(packageName + '.' + file.name.substring(0, file.name.length - 6)))
				}
			}
			return classes
		}

		@Suppress("UNCHECKED_CAST")
		inline fun <reified T : Any> List<*>.checkItemsAre() = if (all { it is T }) this as List<T> else null
	}
}
