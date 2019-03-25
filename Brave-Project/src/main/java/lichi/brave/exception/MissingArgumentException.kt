package lichi.brave.exception

class MissingArgumentException : Exception
{
	constructor() : super()
	constructor (s: String) : super(s)
	constructor (message: String, cause: Throwable) : super(message, cause)
	constructor (cause: Throwable) : super(cause)
}
