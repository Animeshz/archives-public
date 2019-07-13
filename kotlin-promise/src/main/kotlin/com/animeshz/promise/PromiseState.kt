package com.animeshz.promise

enum class PromiseState(val state: Int)
{
	PENDING(0),
	FULFILLED(1),
	REJECTED(2)
}