<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace ClusterPlus\Utils;

class Collector
{
	protected $client;
	private $commands;
	private $modules;
	private $invites;

	function __construct(\CharlotteDunois\Livia\LiviaClient $client)
	{
		$this->client = $client;

		$this->commands = new \CharlotteDunois\Yasmin\Utils\Collection;
		$this->modules = new \CharlotteDunois\Yasmin\Utils\Collection;
		$this->invites = new \CharlotteDunois\Yasmin\Utils\Collection;
	}

	function getCommands(int $guildID, string $name = null)
	{
		return ($name !== null) ? ($this->commands->get($guildID))->get($name) : $this->commands->get($guildID);
	}

	function setCommands(\ClusterPlus\Models\Command ...$commands)
	{
		foreach ($commands as $command) {
			$guildID = $command->guild->id;
			if($this->commands->get($guildID) === null) $this->commands->set($guildID, new \CharlotteDunois\Yasmin\Utils\Collection);
			$cmd = $this->commands->get($guildID);
			$cmd->set($command->name, $command);
			$this->commands->set($guildID, $cmd);
		}
	}

	function loadFromDB()
	{
		$this->client->once('ready', function ()
		{
			$this->client->guilds->each(function ($guild)
			{
				$commands = $this->client->provider->get($guild, 'commands', []);
				foreach ($commands as $command) {
					$cmds[] = \ClusterPlus\Models\Command::jsonUnserialize($this->client, $command);
				}
				if(!empty($cmds)) $this->setCommands($cmds);
			});
		});
	}
}