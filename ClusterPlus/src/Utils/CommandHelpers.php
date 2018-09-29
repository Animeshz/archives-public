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
	public static $options;

	public static $methods;

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

	public static function resolve(string $type, $value, $option){
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
				return \React\Promise\resolve($value->channels->get($option));
			}
			break;

			case 'Role':
			//add support for CommandMessage
			if($value instanceof \CharlotteDunois\Yasmin\Models\Role) {
				return \React\Promise\resolve($value);
			} elseif ($value instanceof \CharlotteDunois\Yasmin\Models\Guild) {
				if(\is_int($option)) return \React\Promise\resolve($value->roles->get($option));
				if(\is_string($option)) return \React\Promise\resolve($value->roles->first(function (\CharlotteDunois\Yasmin\Models\Role $role) {
					if(\mb_strtolower($role->name) === \mb_strtolower($option)) return true;
				}));
			}
			break;
		}
	}

	public static function setupVars()
	{
		$options = \get_class_methods(__CLASS__);
		if (($key = array_search(__FUNCTION__, $options)) !== false) {
			unset($options[$key]);
		}
		self::$options = $options;
		self::$methods = \array_combine(self::$options, [
			['addRole', 'removeRole', 'setVoiceChannel', 'createInvite', 'send', 'setTopic', 'setColor'],
			['GuildMember', 'TextChannel', 'Role']
		]);
	}
}