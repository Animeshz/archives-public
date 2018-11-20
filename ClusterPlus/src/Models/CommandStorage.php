<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Models;

use CharlotteDunois\Yasmin\Models\Guild;
use CharlotteDunois\Collect\Collection;

/**
 * Command Storage
 */
class CommandStorage extends Storage
{
	function resolve($guild, string $name): ?Command
	{
		if ($guild instanceof Guild) $guild = $guild->id;
		
		if($this->has($guild)) {
			$collection = $this->get($guild);

			if ($collection->has($name)) {
				return $collection->get($name);
			}
		}

		return null;
	}

	function store(Command ...$commands): void
	{
		foreach ($commands as $command) {
			$guildID = $command->guild->id;
			if(!$this->has($guildID)) $this->set($guildID, new Collection);
			$cmd = $this->get($guildID);
			$cmd->set($command->name, $command);
		}
	}
}