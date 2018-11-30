<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Utils;

use Animeshz\ClusterPlus\Client;
use Animeshz\ClusterPlus\Models\Command;
use Animeshz\ClusterPlus\Models\CommandStorage;
use Animeshz\ClusterPlus\Models\Invite;
use Animeshz\ClusterPlus\Models\InviteCacheStorage;
use Animeshz\ClusterPlus\Models\InviteStorage;
use Animeshz\ClusterPlus\Models\Module;
use Animeshz\ClusterPlus\Models\ModuleStorage;
use CharlotteDunois\Collect\Collection;
use CharlotteDunois\Livia\Providers\SettingProvider;
use CharlotteDunois\Phoebe\AsyncTask;
use CharlotteDunois\Sarah\SarahWorker;
use CharlotteDunois\Yasmin\Models\ClientBase;
use React\MySQL\Factory;
use React\Promise\Promise;
use React\Promise\ExtendedPromiseInterface;

use function React\Promise\all;

/**
 * A command that can be run in a client.
 *
 * @property \Animeshz\ClusterPlus\Client					$client             The client which initiated the instance.
 * @property \Animeshz\ClusterPlus\Models\CommandStorage	$commands			Collection of commands.
 * @property \CharlotteDunois\Yasmin\Utils\Collection		$modules			Collection of modules.
 * @property \Animeshz\ClusterPlus\Models\InviteStorage		$invites			Collection of invites.
 * @property \CharlotteDunois\Yasmin\Utils\Collection		$inviteCache		Collection of inviteCache.
 */
class Collector implements \Serializable
{
	/**
	 * @var \ClusterPlus\Client
	 */
	protected $client;

	/**
	 * Collection mapped by guild id.
	 * Inner collection contains commands of guild mapped by their names.
	 * 
	 * @var \CharlotteDunois\Collect\Collection
	 */
	protected $commands;

	/**
	 * Collection mapped by guild id.
	 * Inner collection contains modules of guild mapped by their names.
	 * 
	 * @var \CharlotteDunois\Collect\Collection
	 */
	protected $modules;

	/**
	 * Collection mapped by guild id.
	 * Inner collection contains commands of guild mapped by inviter's id.
	 * 
	 * @var \CharlotteDunois\Collect\Collection
	 */
	protected $invites;

	/**
	 * Collection mapped by guild id.
	 * Inner collection contains Yasmin's invites of guild mapped by their code.
	 * 
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

		$this->commands = new CommandStorage($client);
		$this->modules = new ModuleStorage($client);
		$this->invites = new InviteStorage($client);
		$this->inviteCache = new InviteCacheStorage($client);
	}

	/**
	 * @param string  $name
	 * @return bool
	 * @throws \Exception
	 * @internal
	 */
	function __isset($name): bool
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
	function serialize(): string
	{
		$vars = \get_object_vars($this);
		unset($vars['client']);
		return \serialize($vars);
	}

	/**
	 * @return void
	 * @internal
	 */
	function unserialize($vars): void
	{
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
	 * @return \React\Promise\ExtendedPromiseInterface
	 */
	function loadFromDB(): ?ExtendedPromiseInterface
	{
		$task = new class extends AsyncTask
		{
			function run()
			{
				$client = SarahWorker::$client;

				if($client->provider->getState() !== SettingProvider::STATE_READY) {
					return $client->loop->futureTick(array($this, 'run'));
				}

				$fetchedPromises = [];
				$client->guilds->each(function ($guild) use (&$fetchedPromises)
				{
					$fetchedPromises[] = $guild->fetchInvites();
				});

				all($fetchedPromises)->then(function (array $invites) use ($client): Collection
				{
					$invs = $inviteCache = [];

					foreach ($invites as $guildInvites) {
						if($guildInvites->count() === 0) continue;

						$guild = $guildInvites->first()->guild;
						$inviteCache = array_values(array_merge($inviteCache, $guildInvites->all()));

						$newInvites = [];

						$dbInvites = $client->provider->get($guild, 'invites', []);
						$inviteColl = new Collection(\array_column($dbInvites, null, 'code'));

						$newInvites = $guildInvites->filter(function ($invite) use ($inviteColl) {
							return (!$inviteColl->has($invite->code));
						})->map(function ($invite) use ($client) {
							return [$client, $invite];
						})->all();
						$invs = array_merge($invs, array_values($newInvites));
					}

					return new Collection([
						'newInvites' => $invs,
						'inviteCache' => $inviteCache
					]);
				})->then(function (Collection $data) use ($client): Collection
				{
					foreach ($client->guilds as $guild) {

						$invs = $mdls = $cmds = [];
						$invites = $client->provider->get($guild, 'invites', []);
						$modules = $client->provider->get($guild, 'modules', []);
						$commands = $client->provider->get($guild, 'commands', []);

						foreach ($invites as $invite) {
							$invite['guild'] = $guild;
							$invs[] = Invite::jsonUnserialize($client, $invite);
						}
							//problem in here
						foreach ($modules as $module) {
							$module['guild'] = $guild;
							$mdls[] = Module::jsonUnserialize($client, $module);
						}
						foreach ($commands as $command) {
							$command['guild'] = $guild;
							$cmds[] = Command::jsonUnserialize($client, $command);
						}

						if(!empty($invs)) $data = $data->set('invites', \array_merge($data->get('invites') ?? [], $invs));
						if(!empty($mdls)) $data = $data->set('modules', \array_merge($data->get('modules') ?? [], $mdls));
						if(!empty($cmds)) $data = $data->set('commands', \array_merge($data->get('commands') ?? [], $cmds));
					}
					return $data;
				})->then(function ($data) {
					$this->wrap($data);
				}, function (\Exception $e) {
					$this->wrap($e);
				});
			}
		};

		return $this->client->pool->submitTask($task)->then(function (Collection $data)
		{
			$inviteCache = $data->get('inviteCache');
			$invites = $data->get('invites');
			$newInvites = $data->get('newInvites');
			$modules = $data->get('modules');
			$commands = $data->get('commands');

			$newInvites = array_map(function (array $invite) {
				return Invite::make(...$invite);
			}, $newInvites);

			if(!empty($inviteCache)) $this->setInviteCache($inviteCache);
			if(!empty($invites)) $this->setInvites($invites);
			if(!empty($newInvites)) $this->setInvites($newInvites, true);
			if(!empty($modules)) $this->setModules($modules);
			if(!empty($commands)) $this->setCommands($commands);
		}, function (\Exception $error) {
			$this->client->handlePromiseRejection($error);
		});
	}

	/**
	 * Sets commands in local environment. Need to add a unique number identifier before setting to database
	 * @param Command ...$commands 
	 * @return void
	 */
	function setCommands(array $commands, bool $update = false): void
	{
		$this->commands->store($commands, $update);
	}

	function setInvites(array $invites, bool $update = false): void
	{
		$this->invites->store($invites);
	}

	function setInviteCache(array $invites): void
	{
		$this->inviteCache->store($invites);
	}

	function setModules(array $modules): void
	{
		$this->modules->store($modules);
	}
}