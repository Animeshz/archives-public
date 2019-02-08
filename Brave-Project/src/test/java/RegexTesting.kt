import java.util.regex.Pattern

fun main()
{
//	val testPateern = Pattern.compile("(?iu)^(<@!?540189988638163004>\\s+(?:\\Q$\\E\\s*)?|\\Q$\\E\\s*)([^\\s]+)")
	val testPateern = Pattern.compile("(?iu)^(<@!?540189988638163004>\\s+(?:\\Q$\\E\\s*)?|\\Q$\\E\\s*)([^\\s]+)")

	val matcher = testPateern.matcher("\$avatar")
	println(matcher.matches())
	println(matcher.group())
}