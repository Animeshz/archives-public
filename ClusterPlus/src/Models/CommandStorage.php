<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Models;

use Animeshz\ClusterPlus\Client;
use CharlotteDunois\Collect\Collection;
use CharlotteDunois\Yasmin\Models\Guild;

/**
 * Command Storage
 */
class CommandStorage extends Collection
{
	/**
	 * @var \Animeshz\ClusterPlus\Client
	 */
	protected $client;

	function __construct(Client $client)
	{
		$this->client = $client;
	}

	function resolve($guild, $name): ?Command
	{
		if ($guild instanceof Guild) $guild = $guild->id;
		$collection = $this->get($guild);

		if ($collection->has($name)) {
			return $collection->get($name);
		}

		return null;
	}

	function store(Command ...$commands): void
	{
		foreach ($commands as $command) {
			$guildID = $command->guild->id;
			if($this->get($guildID) === null) $this->set($guildID, new Collection);
			$cmd = $this->get($guildID);
			$cmd->set($command->name, $command);
		}
	}
}