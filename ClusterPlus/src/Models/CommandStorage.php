<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Models;

use Animeshz\ClusterPlus\Exceptions\MultipleEntryFoundException;
use CharlotteDunois\Yasmin\Models\Guild;
use InvalidArgumentException;

/**
 * Command Storage
 * 
 * Format this uses is:
 * Guild => Storage<name:Command>
 */
class CommandStorage extends Storage
{
	/**
	 * Resolves instance of command by guild and command name.
	 * 
	 * @param string|CharlotteDunois\Yasmin\Models\Guild	$guild	Guild in which to fetch commands
	 * @param string										$name	Name of the command
	 * @return Animeshz\ClusterPlus\Models\Command|null
	 * @throws Animeshz\ClusterPlus\Exceptions\MultipleEntryFoundException
	 */
	function resolve($guild, string $name): ?Command
	{
		if ($guild instanceof Guild) $guild = $guild->id;
		
		if($this->has($guild)) {
			$context = $this->get($guild);

			if ($context->has($name)) {
				return $context->get($name);
			} else {
				$found = $context->keys()->filter(function ($key) use ($name) {
					return (mb_stripos($key, $name) !== false);
				});

				$count = $found->count();
				if ($count === 1) {
					return $context->get($found->first());
				} elseif ($count > 1) {
					throw new MultipleEntryFoundException("Multiple Commands Found: Try to be more specific");
				}
			}
		}

		return null;
	}

	/**
	 * Stores the commands in local environment by default, supplying second parameter will store it in database too.
	 * 
	 * @param array		$commands	Array of command instances
	 * @param bool		$update		Create the value to database or not
	 * @return void
	 */
	function store(array $commands, bool $update = false): void
	{
		foreach ($commands as $command) {
			if(!$command instanceof Command) $this->client->handlePromiseRejection(new InvalidArgumentException('Command must be instance of Animeshz\ClusterPlus\Models\Command'));
			$guildID = $command->guild->id;
			if(!$this->has($guildID)) $this->set($guildID, new Storage($this->client));
			$cmd = $this->get($guildID);
			$cmd->set($command->name, $command);

			if ($update) { 
				$c = (array) json_decode(json_encode($command));
				$c = array_filter($c, function ($value, $key) { return $key !== 'guild'; });

				$dbCmds = $this->client->provider->get($command->guild, 'commands', []);
				$dbCmds[] = $c;
				$this->client->provider->set($command->guild, 'commands', $dbCmds);
			}
		}
	}
}