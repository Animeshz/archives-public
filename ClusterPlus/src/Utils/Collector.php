<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Utils;

use \Animeshz\ClusterPlus\Client;
use \Animeshz\ClusterPlus\Models\Command;
use \Animeshz\ClusterPlus\Models\Invite;
use \Animeshz\ClusterPlus\Models\Module;
use \CharlotteDunois\Collect\Collection;
use \React\Promise\Promise;
use \React\Promise\PromiseInterface;

/**
 * A command that can be run in a client.
 *
 * @property \Animeshz\ClusterPlus\Client					$client             The client which initiated the instance.
 * @property \CharlotteDunois\Yasmin\Utils\Collection		$commands			Collection of commands.
 * @property \CharlotteDunois\Yasmin\Utils\Collection		$modules			Collection of modules.
 * @property \CharlotteDunois\Yasmin\Utils\Collection		$invites			Collection of invites.
 * @property \CharlotteDunois\Yasmin\Utils\Collection		$inviteCache		Collection of inviteCache.
 */
class Collector
{
	/**
	 * @var \ClusterPlus\Client
	 */
	protected $client;

	/**
	 * @var \CharlotteDunois\Collect\Collection
	 */
	protected $commands;

	/**
	 * @var \CharlotteDunois\Collect\Collection
	 */
	protected $modules;

	/**
	 * @var \CharlotteDunois\Collect\Collection
	 */
	protected $invites;

	/**
	 * @var \CharlotteDunois\Collect\Collection
	 */
	protected $inviteCache;

	/**
	 * Constructor
	 * 
	 * @param Client $client 
	 */
	function __construct(Client $client)
	{
		$this->client = $client;

		$this->commands = new Collection;
		$this->modules = new Collection;
		$this->invites = new Collection;
		$this->inviteCache = new Collection;
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

	/**
	 * Fetch Command from local environment, if second parameter is
	 * set it'll return command, else return collection of commands.
	 * 
	 * @param int $guildID 
	 * @param string|null $name 
	 * @return \CharlotteDunois\Yasmin\Utils\Collection|\Animeshz\ClusterPlus\Models\Commands
	 */
	function getCommands(int $guildID, string $name = null)
	{
		return ($name !== null) ? ($this->commands->get($guildID))->get($name) : $this->commands->get($guildID);
	}

	/**
	 * Fetch Invite from local environment, if second parameter is
	 * set it'll return invite, else return collection of invites.
	 * 
	 * @param int $guildID 
	 * @param string|null $name 
	 * @return \CharlotteDunois\Yasmin\Utils\Collection|\Animeshz\ClusterPlus\Models\Invite
	 */
	function getInvites(int $guildID, string $name = null)
	{
		return ($name !== null) ? ($this->commands->get($guildID))->get($name) : $this->commands->get($guildID);
	}

	/**
	 * Fetch Modules from local environment, if second parameter
	 * is set it'll return module, else return collection of modules.
	 * 
	 * @param int $guildID 
	 * @param string|null $name 
	 * @return \CharlotteDunois\Yasmin\Utils\Collection|\Animeshz\ClusterPlus\Models\Modules
	 */
	function getModules(int $guildID, string $name = null)
	{
		return ($name !== null) ? ($this->commands->get($guildID))->get($name) : $this->commands->get($guildID);
	}

	/**
	 * Loads commands, modules and invites from databases.
	 * $client->provider must be set before calling this function.
	 * 
	 * @return \React\Promise\PromiseInterface
	 */
	function loadFromDB(): PromiseInterface
	{
		return (new Promise(function(callable $resolve, callable $reject)
		{
			$this->client->once('ready', function ()
			{
				try {
					$this->client->guilds->each(function ($guild)
					{
						$invs = $mdls = $cmds = [];
						$invites = $this->client->provider->get($guild, 'invites', []);
						$modules = $this->client->provider->get($guild, 'modules', []);
						$commands = $this->client->provider->get($guild, 'commands', []);

						foreach ($invites as $invite) {
							$invs[] = Invite::jsonUnserialize($this->client, $invite);
						}
						foreach ($modules as $module) {
							$mdls[] = Module::jsonUnserialize($this->client, $module);
						}
						foreach ($commands as $command) {
							$cmds[] = Command::jsonUnserialize($this->client, $command);
						}

						if(!empty($invs)) $this->setInvites($invs);
						if(!empty($mdls)) $this->setModules($mdls);
						if(!empty($cmds)) $this->setCommands($cmds);
					});
					$resolve();
				} catch (\Exception $e) {
					$reject($e);
				}
			});
		}));	
	}

	function loadInviteCache(): PromiseInterface
	{
		return (new Promise(function (callable $resolve, callable $reject){
			$this->client->guilds->each(function ($guild)
			{
				$guild->fetchInvites()->done(function ($invites)
				{
					$this->inviteCache->set($guild->id, $invites);
				});
			});
		}));
	}

	/**
	 * Sets commands in local environment. Need to add a unique number identifier before setting to database
	 * @param Command ...$commands 
	 * @return type
	 */
	function setCommands(Command ...$commands)
	{
		foreach ($commands as $command) {
			$guildID = $command->guild->id;
			if($this->commands->get($guildID) === null) $this->commands->set($guildID, new \CharlotteDunois\Yasmin\Utils\Collection);
			$cmd = $this->commands->get($guildID);
			$cmd->set($command->name, $command);
			$this->commands->set($guildID, $cmd);
		}
	}

	// function setInvites(Invite ...$invites)
	// {
	// 	foreach ($invites as $invite) {
	// 		$guildID = $invite->guild->id;
	// 		if($this->invites->get($guildID) === null) $this->invites->set($guildID, new \CharlotteDunois\Yasmin\Utils\Collection);
	// 		$cmd = $this->invites->get($guildID);
	// 		$cmd->set($invite->name, $invite);
	// 		$this->invites->set($guildID, $cmd);
	// 	}
	// }

	// function setModules(Module ...$modules)
	// {
	// 	foreach ($modules as $module) {
	// 		$guildID = $module->guild->id;
	// 		if($this->modules->get($guildID) === null) $this->modules->set($guildID, new \CharlotteDunois\Yasmin\Utils\Collection);
	// 		$cmd = $this->modules->get($guildID);
	// 		$cmd->set($module->name, $module);
	// 		$this->modules->set($guildID, $cmd);
	// 	}
	// }
}