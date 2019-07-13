package com.animeshz.promise

import com.animeshz.promise.PromiseInterface
import com.animeshz.promise.resolve
import io.kotlintest.matchers.boolean.shouldBeTrue
import io.kotlintest.matchers.types.shouldBeInstanceOf
import io.kotlintest.shouldBe
import io.kotlintest.shouldThrow
import io.kotlintest.specs.StringSpec

class FulfilledPromiseTest : StringSpec({

	"always code should run in sequence and return the PromiseInterface of same value" {
		var always = false
		resolve(4852)
			.then {
				2 * it as Int
			}
			.always {
				always = true
			}
			.then {
				it.shouldBe(9704)
			}
			.shouldBeInstanceOf<PromiseInterface>()
		always.shouldBeTrue()
	}

	"FulfilledPromise.done should pass the Exception/Error of onResolve" {
		shouldThrow<Throwable> {
			resolve(4852).done {
				throw Exception("An error occurred")
			}
		}
	}

})