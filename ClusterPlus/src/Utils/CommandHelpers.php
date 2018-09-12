<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace ClusterPlus\Utils;

function addAction(string $name, ...$values)
{
	switch (true) {
		case ($name === 'addRole' || $name === 'removeRole' || $name === 'setVoiceChannel'):
		if(!($values[0] instanceof \React\Promise\ExtendedPromiseInterface)) {
			if (!($values[0] instanceof \CharlotteDunois\Yasmin\Models\GuildMember)) throw new InvalidArgumentException("Given value is not an instance of CharlotteDunois\Yasmin\Models\GuildMember");
			return $values[0]->$name($values[1]);
		}
		return $values[0]->then(function (\CharlotteDunois\Yasmin\Models\GuildMember $member) use ($name, $values)
		{
			return $member->$name($values[1]);
		});
		
		break;

		case ($name === 'createInvite' || $name === 'send' || $name === 'setTopic'):
		if(!($values[0] instanceof \React\Promise\ExtendedPromiseInterface)) {
			if (!($values[0] instanceof \CharlotteDunois\Yasmin\Models\TextChannel)) throw new InvalidArgumentException("Given value is not an instance of CharlotteDunois\Yasmin\Models\TextChannel");
			return $values[0]->$name($values[1]);
		}
		return $values[0]->then(function (\CharlotteDunois\Yasmin\Models\TextChannel $channel) use ($name, $values)
		{
			return $channel->$name($values[1]);
		});
		break;

		case ($name === 'setColor'):
		if(!($values[0] instanceof \React\Promise\ExtendedPromiseInterface)) {
			if (!($values[0] instanceof \CharlotteDunois\Yasmin\Models\Role)) throw new InvalidArgumentException("Given value is not an instance of CharlotteDunois\Yasmin\Models\TextChannel");
			return $values[0]->$name($values[1]);
		}
		return $values[0]->then(function (\CharlotteDunois\Yasmin\Models\Role $role) use ($name, $values)
		{
			return $role->$name($values[1]);
		});
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
		} elseif ($value instanceof \CharlotteDunois\Livia\CommandMessage) {
			return $value->message->guild->fetchMember($value->message->author->id);
		}
		break;

		case 'TextChannel':
		if($value instanceof \CharlotteDunois\Yasmin\Models\TextChannel) {
			return \React\Promise\resolve($value);
		} elseif ($value instanceof \CharlotteDunois\Livia\CommandMessage) {
			return \React\Promise\resolve($value->message->channel);
		} elseif (($value instanceof \CharlotteDunois\Yasmin\Models\Guild) || ($value instanceof \CharlotteDunois\Yasmin\Client)) {
			return \React\Promise\resolve($value->channels->get($options[0]));
		}
		break;

		case 'Role':
		if($value instanceof \CharlotteDunois\Yasmin\Models\Role) {
			return \React\Promise\resolve($value);
		} elseif ($value instanceof \CharlotteDunois\Yasmin\Models\Guild) {
			if(\is_int($options[0])) return \React\Promise\resolve($value->roles->get($options[0]));
			if(\is_string($options[0])) return \React\Promise\resolve($value->roles->first(function (\CharlotteDunois\Yasmin\Models\Role $role) {
				if(\mb_strtolower($role->name) === \mb_strtolower($options[0])) return true;
			}));
		}
		break;
	}
}