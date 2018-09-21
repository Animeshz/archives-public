<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace ClusterPlus\Utils;

class CommandHelpers
{
	public static decodeArgs(array $args)
	{}

	public static function addAction(string $name, $model, $option)
	{
		if(!($model instanceof \React\Promise\ExtendedPromiseInterface)) {
			$model = \React\Promise\resolve($model);
		}

		switch (true) {
			case ($name === 'addRole' || $name === 'removeRole' || $name === 'setVoiceChannel'):
			return $model->then(function (\CharlotteDunois\Yasmin\Models\GuildMember $member) use ($name, $option)
			{
				return $member->$name($option);
			});
			break;

			case ($name === 'createInvite' || $name === 'send' || $name === 'setTopic'):
			return $model->then(function (\CharlotteDunois\Yasmin\Models\TextChannel $channel) use ($name, $option)
			{
				return $channel->$name($option);
			});
			break;

			case ($name === 'setColor'):
			return $model->then(function (\CharlotteDunois\Yasmin\Models\Role $role) use ($name, $option)
			{
				return $role->$name($option);
			});
			break;
		}
	}

// addTimer(int $time, $name, $value)
// addListener()

	public static function resolve(string $type, $value, ...$options){
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
			//add support for CommandMessage
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
}