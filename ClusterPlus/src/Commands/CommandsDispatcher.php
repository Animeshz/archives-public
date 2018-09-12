<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace ClusterPlus\Commands;

class CommandsDispatcher
{
	/**
	 * @var \CharlotteDunois\Livia\LiviaClient<\CharlotteDunois\Yasmin\Client>
	 */
	protected $client;

	public function __construct(\CharlotteDunois\Yasmin\Client $client)
	{
		$this->client = $client;

		$this->client->registry->registerDefaults();
		$this->registerGroups()->registerCommands();
	}

	public function registerGroups()
	{
		$this->client->registry->registerGroup(
			(new \CharlotteDunois\Livia\Commands\CommandGroup($this->client, 'clusterplus_moderation', 'Cluster Plus Moderation', true)),
			(new \CharlotteDunois\Livia\Commands\CommandGroup($this->client, 'clusterplus_polls', 'Cluster Plus Polls')),
			(new \CharlotteDunois\Livia\Commands\CommandGroup($this->client, 'clusterplus_forms', 'Cluster Plus Forms')),
			(new \CharlotteDunois\Livia\Commands\CommandGroup($this->client, 'clusterplus_meta', 'Cluster Plus Meta'))
		);
		return $this;
	}

	public function registerCommands()
	{
		$this->client->registry->registerCommandsIn(__DIR__.'/commands', true);
		return $this;
	}
}