<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Utils;

use \Animeshz\ClusterPlus\Client;
use \Animeshz\ClusterPlus\Dependent\Worker;
use \Animeshz\ClusterPlus\Models\Command;
use \Animeshz\ClusterPlus\Models\Invite;
use \Animeshz\ClusterPlus\Models\Module;
use \CharlotteDunois\Collect\Collection;
use \CharlotteDunois\Phoebe\AsyncTask;
use \CharlotteDunois\Yasmin\Models\ClientBase;
use \React\MySQL\Factory;
use \React\Promise\Promise;
use \React\Promise\ExtendedPromiseInterface;

/**
 * A command that can be run in a client.
 *
 * @property \Animeshz\ClusterPlus\Client					$client             The client which initiated the instance.
 * @property \CharlotteDunois\Yasmin\Utils\Collection		$commands			Collection of commands.
 * @property \CharlotteDunois\Yasmin\Utils\Collection		$modules			Collection of modules.
 * @property \CharlotteDunois\Yasmin\Utils\Collection		$invites			Collection of invites.
 * @property \CharlotteDunois\Yasmin\Utils\Collection		$inviteCache		Collection of inviteCache.
 */
class Collector implements \Serializable
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
	 * @return string
	 * @internal
	 */
	function serialize()
	{
		$vars = \get_object_vars($this);
		unset($vars['client']);
		return \serialize($vars);
	}

	/**
	 * @return void
	 * @internal
	 */
	function unserialize($vars) {
		if(ClientBase::$serializeClient === null) {
			throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
		}
		
		$vars = \unserialize($vars);
		foreach($vars as $name => $val) {
			$this->$name = $val;
		}
		
		$this->client = ClientBase::$serializeClient;
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
	function loadFromDB(): ExtendedPromiseInterface
	{
		return (new Promise(function (callable $resolve, callable $reject)
		{
			$loader = function () use ($resolve, $reject)
			{
				try {
					$this->client->guilds->each(function ($guild)
					{
						$guild->fetchInvites()->done(function ($invites) use ($guild)
						{
							$this->inviteCache->set($guild->id, $invites);
							$new = [];
							$invites->each(function ($invite) use ($guild, $new)
							{
								$invites = $this->provider->get($guild, 'invites', []);
								$invites = \array_map(function ($invite)
								{
									return $invite['code'];
								}, $invites);
								if(!\in_array($invite->code, $invites)) {
									$new[] = Invite::make($this->client, $invite);
								}
							});
							if(!empty($new)) $this->setInvites($new);
						});

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

						if(!empty($invs)) $this->setInvites(...$invs);
						if(!empty($mdls)) $this->setModules(...$mdls);
						if(!empty($cmds)) $this->setCommands(...$cmds);
					});
					$resolve();
				} catch (\Exception $e) {
					$reject($e);
				}
			};

			if($this->client->readyTimestamp === null) {
				$this->client->once('ready', $loader);
			} else {
				$loader();
			}
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
			if($this->commands->get($guildID) === null) $this->commands->set($guildID, new Collection);
			$cmd = $this->commands->get($guildID);
			$cmd->set($command->name, $command);
			$this->commands->set($guildID, $cmd);
		}
	}

	function setInvites(Invite ...$invites)
	{
		foreach ($invites as $invite) {
			$guildID = $invite->guild->id;
			if($this->invites->get($guildID) === null) $this->invites->set($guildID, new Collection);
			$inv = $this->invites->get($guildID);
			$inv->set($invite->code, $invite);
			$this->invites->set($guildID, $inv);
		}
	}

	// function setModules(Module ...$modules)
	// {
	// 	foreach ($modules as $module) {
	// 		$guildID = $module->guild->id;
	// 		if($this->modules->get($guildID) === null) $this->modules->set($guildID, new Collection);
	// 		$cmd = $this->modules->get($guildID);
	// 		$cmd->set($module->name, $module);
	// 		$this->modules->set($guildID, $cmd);
	// 	}
	// }
}