package com.animeshz.promise

import io.kotlintest.matchers.types.shouldBeTypeOf
import io.kotlintest.matchers.types.shouldNotBeNull
import io.kotlintest.shouldBe
import io.kotlintest.specs.StringSpec

class FunctionsTest : StringSpec({

	"resolve should return FulfilledPromise" {
		resolve(4852).shouldBeTypeOf<FulfilledPromise>()
	}

	"reject should return RejectedPromise" {
		reject(Exception("Exception")).shouldBeTypeOf<RejectedPromise>()
	}

	"all should return all values as promise in List" {
		val values = listOf("Hello", "World", "Hi", "Hey")
		val promises = listOf(
			Promise({ resolve, _ -> resolve("Hello") }),
			Promise({ resolve, _ -> resolve("World") }),
			Promise({ resolve, _ -> resolve("Hi") }),
			Promise({ resolve, _ -> resolve("Hey") })
		)

		all(promises).then {
			it.shouldBe(values)
		}
	}

	"all should reject regardless of other values if any one of the promise rejects" {
		var result: Throwable? = null
		val error = Exception("Something went wrong")
		val promises = listOf(
			Promise({ resolve, _ -> resolve("Hello") }),
			Promise({ resolve, _ -> resolve("World") }),
			Promise({ resolve, _ -> resolve("Hi") }),
			Promise({ _, reject -> reject(error) })
		)

		all(promises).otherwise {
			result = it
			Unit
		}

		result.shouldNotBeNull()
		result.shouldBe(error)
	}

})