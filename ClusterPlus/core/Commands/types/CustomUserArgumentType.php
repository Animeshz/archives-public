<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace ClusterPlus\Commands\types;

class CustomUserArgumentType extends \CharlotteDunois\Livia\Types\ArgumentType
{
	function __construct(\CharlotteDunois\Livia\LiviaClient $client)
	{
		parent::__construct($client, 'c-user');
	}

	function validate(string $value, \CharlotteDunois\Livia\CommandMessage $message, ?\CharlotteDunois\Livia\Arguments\Argument $arg = null) {
		$prg = \preg_match('/(?:<@!?)?(\d{15,})>?/', $value, $matches);
		if($prg === 1) {
			return $message->client->fetchUser($matches[1])->then(function () {
				return true;
			}, function () {
				return false;
			});
		}

		$search = \mb_strtolower($value);

		$inexactUsers = $this->client->users->filter(function ($user) use ($search) {
			return (\mb_stripos($user->tag, $search) !== false);
		});
		$inexactLength = $inexactUsers->count();

		if($inexactLength === 0) {
			return false;
		}
		if($inexactLength === 1) {
			return true;
		}

		$exactUsers = $this->client->users->filter(function ($user) use ($search) {
			return (\mb_strtolower($user->tag) === $search);
		});
		$exactLength = $exactUsers->count();

		if($exactLength === 1) {
			return true;
		}

		if($exactLength > 0) {
			$users = $exactUsers;
		} else {
			$users = $inexactUsers;
		}

		if($users->count() >= 15) {
			return 'Multiple users found. Please be more specific.';
		}

		$users = $users->map(function (\CharlotteDunois\Yasmin\Models\User $user) {
			return $user->tag;
		});

		return \CharlotteDunois\Livia\Utils\DataHelpers::disambiguation($users, 'users', null).\PHP_EOL;
	}

	function parse(string $value, \CharlotteDunois\Livia\CommandMessage $message, ?\CharlotteDunois\Livia\Arguments\Argument $arg = null) {
        $prg = \preg_match('/(?:<@!?)?(\d{15,})>?/', $value, $matches);
        if($prg === 1) {
            return $this->client->users->get($matches[1]);
        }
        
        $search = \mb_strtolower($value);
        
        $inexactUsers = $this->client->users->filter(function ($user) use ($search) {
            return (\mb_stripos($user->tag, $search) !== false);
        });
        $inexactLength = $inexactUsers->count();
        
        if($inexactLength === 0) {
             return null;
        }
        if($inexactLength === 1) {
            return $inexactUsers->first();
        }
        
        $exactUsers = $this->client->users->filter(function ($user) use ($search) {
            return (\mb_strtolower($user->tag) === $search);
        });
        $exactLength = $exactUsers->count();
        
        if($exactLength === 1) {
            return $exactUsers->first();
        }
        
        return null;
    }
}