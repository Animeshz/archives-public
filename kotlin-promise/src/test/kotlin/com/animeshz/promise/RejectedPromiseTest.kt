package com.animeshz.promise

import io.kotlintest.matchers.boolean.shouldBeTrue
import io.kotlintest.matchers.types.shouldBeInstanceOf
import io.kotlintest.shouldBe
import io.kotlintest.shouldThrow
import io.kotlintest.specs.StringSpec

class RejectedPromiseTest: StringSpec ({

	"always code should run in sequence and return the PromiseInterface of same reason" {
		var always1 = false
		var always2 = false
		reject(Exception("This is exception"))
			.always {
				always1 = true
			}
			.otherwise(Exception::class) {
				it.message
			}
			.always {
				always2 = true
			}
			.then {
				it.shouldBe("This is exception")
			}
			.shouldBeInstanceOf<PromiseInterface>()

		always1.shouldBeTrue()
		always2.shouldBeTrue()
	}

	"RejectedPromise.done should pass the Exception/Error of onResolve" {
		val exception = shouldThrow<Throwable> {
			reject(Exception("Exception")).done (null, {
				val exception = it as Exception
				throw Exception("Nested " + exception.message)
			})
		}

		exception.message.shouldBe("Nested Exception")
	}

})