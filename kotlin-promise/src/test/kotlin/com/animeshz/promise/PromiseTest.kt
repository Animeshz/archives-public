package com.animeshz.promise

import io.kotlintest.matchers.boolean.shouldBeFalse
import io.kotlintest.matchers.boolean.shouldBeTrue
import io.kotlintest.matchers.types.shouldBeInstanceOf
import io.kotlintest.matchers.types.shouldBeNull
import io.kotlintest.matchers.types.shouldBeTypeOf
import io.kotlintest.shouldBe
import io.kotlintest.specs.StringSpec
import java.io.IOException

class PromiseTest : StringSpec({

	"PromiseInterface.done should return Unit" {
		resolve(4852).done().shouldBeTypeOf<Unit>()

		resolve(4852).done {
			//do some task with it i.e. value
		}.shouldBeTypeOf<Unit>()

		resolve(4852).done({
			//do some task with it i.e. value
		}, {
			//log error or just throw it
			throw it
		}).shouldBeTypeOf<Unit>()
	}

	"otherwise should catch only the exception provided, if not provided it should eat any exception" {
		var execution1 = false
		var execution2 = false
		var execution3 = false

		Promise({ _, reject -> reject(Exception("Something went wrong - 1")) }).otherwise(IOException::class) { execution1 = true; Unit }.shouldBeInstanceOf<PromiseInterface>()
		Promise({ _, reject -> reject(IOException("Something went wrong - 2")) }).otherwise(IOException::class) { execution2 = true; Unit }.shouldBeInstanceOf<PromiseInterface>()
		Promise({ _, reject -> reject(Exception("Something went wrong - 3")) }).otherwise { execution3 = true; Unit }.shouldBeInstanceOf<PromiseInterface>()

		execution1.shouldBeFalse()
		execution2.shouldBeTrue()
		execution3.shouldBeTrue()
	}

	"otherwise unhandled error should not be suppressed and should not be passed to another otherwise once given" {
		var ioexcepton = false
		var exception = false
		var throwable = false

		Promise({ _, reject -> reject(Exception("Unsuppressed")) }).otherwise(IOException::class) { ioexcepton = true; Unit }.otherwise(Exception::class) { exception = true; Unit }.otherwise { throwable = true; Unit }

		ioexcepton.shouldBeFalse()
		exception.shouldBeTrue()
		throwable.shouldBeFalse()
	}

	"otherwise value should be resolved or rejected upon next chain of then/done" {
		var value1 = ""
		var value2 = ""

		Promise({ _, reject -> reject(Exception("Exception")) }).otherwise(Exception::class) { "hello" }.then { value1 = it as String; Unit }
		Promise({ _, reject -> reject(Exception("Exception")) }).otherwise(Exception::class) { throw IOException(it.message) }.otherwise(IOException::class) { value2 = it.message!!; Unit }

		value1.shouldBe("hello")
		value2.shouldBe("Exception")
	}

	"single promise can have multiple handlers" {
		var result: String? = null
		val promise = Promise({ resolve, _ -> resolve("Hello World!") })

		result.shouldBeNull()

		promise.then {
			if (it is String) result = it
		}

		result.shouldBe("Hello World!")

		promise.then {
			result = "Foo Bar"
			Unit
		}

		result.shouldBe("Foo Bar")
	}

	"promise state should update once result has came" {
		Promise({ resolve, _ -> resolve("Hello World!") }).state.shouldBe(PromiseState.FULFILLED)
		Promise({ _, reject -> reject(Exception("Bad result")) }).state.shouldBe(PromiseState.REJECTED)
	}

})