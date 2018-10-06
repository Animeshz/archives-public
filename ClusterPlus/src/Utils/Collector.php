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
	protected $commands;
	protected $modules;
	protected $invites;
	protected $inviteCache;

	function __construct(\CharlotteDunois\Livia\LiviaClient $client)
	{
		$this->client = $client;

		$this->commands = new \CharlotteDunois\Yasmin\Utils\Collection;
		$this->modules = new \CharlotteDunois\Yasmin\Utils\Collection;
		$this->invites = new \CharlotteDunois\Yasmin\Utils\Collection;
	}

		/**
	 * @param string  $name
	 * @return bool
	 * @throws \Exception
	 * @internal
	 */
	function __isset($name)
	{
		try {
			return $this->$name !== null;
		} catch (\RuntimeException $e) {
			if($e->getTrace()[0]['function'] === '__get') {
				return false;
			}
			
			throw $e;
		}
	}
	
	/**
	 * @param string  $name
	 * @return mixed
	 * @throws \RuntimeException
	 * @internal
	 */
	function __get($name)
	{
		if(\property_exists($this, $name)) {
			return $this->$name;
		}		
		throw new \RuntimeException('Unknown property '.\get_class($this).'::$'.$name);
	}

	function getCommands(int $guildID, string $name = null)
	{
		return ($name !== null) ? ($this->commands->get($guildID))->get($name) : $this->commands->get($guildID);
	}

	function getInvites(int $guildID, string $name = null)
	{
		return ($name !== null) ? ($this->commands->get($guildID))->get($name) : $this->commands->get($guildID);
	}

	function getModules(int $guildID, string $name = null)
	{
		return ($name !== null) ? ($this->commands->get($guildID))->get($name) : $this->commands->get($guildID);
	}

	function loadFromDB()
	{
		$this->client->once('ready', function ()
		{
			$this->client->guilds->each(function ($guild)
			{
				$invs = $mdls = $cmds = [];
				$invites = $this->client->provider->get($guild, 'invites', []);
				$modules = $this->client->provider->get($guild, 'modules', []);
				$commands = $this->client->provider->get($guild, 'commands', []);

				foreach ($invites as $invite) {
					$invs[] = \ClusterPlus\Models\Invite::jsonUnserialize($this->client, $invite);
				}
				foreach ($modules as $module) {
					$mdls[] = \ClusterPlus\Models\Module::jsonUnserialize($this->client, $module);
				}
				foreach ($commands as $command) {
					$cmds[] = \ClusterPlus\Models\Command::jsonUnserialize($this->client, $command);
				}
				
				if(!empty($invs)) $this->setInvites($invs);
				if(!empty($mdls)) $this->setModules($mdls);
				if(!empty($cmds)) $this->setCommands($cmds);
			});
		});
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

	function setInvites(\ClusterPlus\Models\Command ...$commands)
	{
		foreach ($commands as $command) {
			$guildID = $command->guild->id;
			if($this->commands->get($guildID) === null) $this->commands->set($guildID, new \CharlotteDunois\Yasmin\Utils\Collection);
			$cmd = $this->commands->get($guildID);
			$cmd->set($command->name, $command);
			$this->commands->set($guildID, $cmd);
		}
	}

	function setModules(\ClusterPlus\Models\Command ...$commands)
	{
		foreach ($commands as $command) {
			$guildID = $command->guild->id;
			if($this->commands->get($guildID) === null) $this->commands->set($guildID, new \CharlotteDunois\Yasmin\Utils\Collection);
			$cmd = $this->commands->get($guildID);
			$cmd->set($command->name, $command);
			$this->commands->set($guildID, $cmd);
		}
	}
}