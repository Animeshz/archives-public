<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace ClusterPlus\Utils;

function addAction(string $name, ...$values = null)
{
	switch (true) {
		case ($name === 'addRole' || $name === 'removeRole' || $name === 'setVoiceChannel'):
		if (!($values[0] instanceof \CharlotteDunois\Yasmin\Models\GuildMember)) {
			if(!($values[0] instanceof \React\Promise\ExtendedPromiseInterface)) throw new InvalidArgumentException("Given value is not an instance of CharlotteDunois\Yasmin\Models\GuildMember");
			return $values[0]->then(function (\CharlotteDunois\Yasmin\Models\GuildMember $member) use ($name, $values)
			{
				$member->$name($values[1]);
			});
		}
		return $values[0]->$name($values[1]);
		break;

		case ($name === 'createInvite' || $name === 'send' || $name === 'setTopic'):
		if (!($values[0] instanceof \CharlotteDunois\Yasmin\Models\TextChannel)) throw new InvalidArgumentException("Given value is not an instance of CharlotteDunois\Yasmin\Models\TextChannel");
		return $values[0]->$name($values[1]);
		break;

		case ($name === 'setColor'):
		if (!($values[0] instanceof \CharlotteDunois\Yasmin\Models\Role)) throw new InvalidArgumentException("Given value is not an instance of CharlotteDunois\Yasmin\Models\TextChannel");
		return $values[0]->$name($values[1]);
		break;
	}
}

// addTimer(int $time, $name, $value)
// addListener()

function resolve(string $type, $value, ...$options){
	switch ($type) {
		case 'GuildMember':
		if($value instanceof \CharlotteDunois\Yasmin\Models\GuildMember) {
			return \React\Promise\resolve($value);
		} elseif ($value instanceof \CharlotteDunois\Yasmin\Models\Guild) {
			if(\is_string($options[0])) return $value->fetchMember($options[0]);
			if($options[0] instanceof \CharlotteDunois\Yasmin\Models\User) return $value->fetchMember($options[0]->id);
		}
		break;

		case 'TextChannel':
		if($value instanceof \CharlotteDunois\Yasmin\Models\TextChannel) {
			return $value;
		} elseif (($value instanceof \CharlotteDunois\Yasmin\Models\Guild) || ($value instanceof \CharlotteDunois\Yasmin\Client)) {
			if(\is_int($options[1])) return $value->channels->get($options[1]);
		}
		break;

		case 'Role':
		if($value instanceof \CharlotteDunois\Yasmin\Models\Role) {
			return $value;
		} elseif ($value instanceof \CharlotteDunois\Yasmin\Models\Guild) {
			if(\is_int($options[1])) return $value->roles->get($options[1]);
		}
		break;
	}
}