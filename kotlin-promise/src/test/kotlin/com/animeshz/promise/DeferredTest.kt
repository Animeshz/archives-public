package com.animeshz.promise

import io.kotlintest.matchers.string.shouldBeEmpty
import io.kotlintest.matchers.types.shouldBeNull
import io.kotlintest.shouldBe
import io.kotlintest.specs.StringSpec

class DeferredTest: StringSpec({

	"resolve callback triggered when deferred is resolved" {
		var result = ""
		var error: Throwable? = null
		val deferred = Deferred()

		deferred.promise().then ({
			if(it is String) result = it
		}, {
			error = it
			Unit
		})

		result.shouldBeEmpty()
		error.shouldBeNull()

		deferred.resolve("Yee-ha")
		result.shouldBe("Yee-ha")
		error.shouldBeNull()
	}

	"reject callback triggered when deferred is rejected" {
		var result = ""
		val e = Exception("something went wrong")
		var error: Throwable? = null
		val deferred = Deferred()

		deferred.promise().then ({
			if (it is String) result = it
		}, {
			error = it
			Unit
		})

		result.shouldBeEmpty()
		error.shouldBeNull()

		deferred.reject(e)
		result.shouldBeEmpty()
		error.shouldBe(e)
	}

	"Promise of deferred should be in pending state till it is resolved or rejected" {
		val resolvingDeferred = Deferred()
		val rejectingDeferred = Deferred()

		resolvingDeferred.promise().state.shouldBe(PromiseState.PENDING)
		resolvingDeferred.resolve("Hello")
		resolvingDeferred.promise().state.shouldBe(PromiseState.FULFILLED)

		rejectingDeferred.promise().state.shouldBe(PromiseState.PENDING)
		rejectingDeferred.reject(Exception("Hmm"))
		rejectingDeferred.promise().state.shouldBe(PromiseState.REJECTED)
	}

})