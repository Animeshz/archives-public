<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Dependent;

use \Animeshz\ClusterPlus\Client;
use \CharlotteDunois\Yasmin\Models\ClientBase;

/**
 * Attaches listener to the client
 */
class EventHandler implements \Animeshz\ClusterPlus\Interfaces\EventHandler, \Serializable
{
	/**
	 * @var \Animeshz\ClusterPlus\Client<\CharlotteDunois\Livia\LiviaClient>
	 */
	protected $client;

	/**
	 * @var string[]
	 */
	protected $exclude;

	/**
	 * Constructor.
	 * @param \Animeshz\ClusterPlus\Client		$client			Client who initiated application
	 * @param string[]							$excludeFuncs	Event to exclude
	 */
	public function __construct(Client $client, ?array $excludeFuncs = null)
	{
		$this->client = $client;

		$exclude = [
			'__construct',
			'__destruct',
			'__call',
			'__callStatic',
			'__get',
			'__set',
			'__isset',
			'__unset',
			'__sleep',
			'__wakeup',
			'__toString',
			'__invoke',
			'__set_state',
			'__clone',
			'__debugInfo',
			'dispatch',
			'serialize',
			'unserialize'
		];
		$exclude = ($excludeFuncs !== null ? array_merge($exclude, $excludeFuncs) : $exclude);
		$this->exclude = $exclude;
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
	function unserialize($data): void
	{
		if(ClientBase::$serializeClient === null) {
			throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
		}
		$vars = \unserialize($data);

		foreach ($vars as $key => $value) {
			$this->key = $value;
		}
		
		$this->client = ClientBase::$serializeClient;
	}

	/**
	 * @return array
	 * @internal
	 */
	protected function ready(): array
	{
		return [
			__FUNCTION__,
			function ()
			{
				$this->client->user->setGame($this->client->getOption('game') ?? 'Waving hands, having fun.');
				echo 'Logged in as '.$this->client->user->tag.' created on '.$this->client->user->createdAt->format('d.m.Y H:i:s').PHP_EOL;
			}
		];
	}

	/**
	 * Dispatches all the events
	 * 
	 * @return void
	 * @internal
	 */
	final public function dispatch(): void
	{
		foreach (get_class_methods($this) as $event) {
			if(in_array($event, $this->exclude)){ continue; }
			$e = $this->$event();
			if(is_array($e) && is_callable($e[1])) {
				$events[] = $e;
			}
		}
		foreach ($events as $event) {
			$this->client->on($event[0], $event[1]);
		}
	}

	/**
	 * @return array
	 * @internal
	 */
	private function debug()
	{
		return [
			__FUNCTION__,
			function ($debug)
			{
				echo $debug.\PHP_EOL;
			}
		];
	}

	/**
	 * @return array
	 * @internal
	 */
	private function error(): array
	{
		return [
			__FUNCTION__,
			function (\Throwable $error)
			{
				echo $error->getMessage().\PHP_EOL.$error->getFile().'in line no. '.$error->getLine().\PHP_EOL;
			}
		];
	}

	/**
	 * @return array
	 * @internal
	 */
	private function providerSet(): array
	{
		return [
			__FUNCTION__,
			function (): void
			{
				$this->client->collector->loadFromDB()->otherwise(function (\Exception $e) { $this->client->handlePromiseRejection($e); });
			}
		];
	}

	/**
	 * Dump every packet recieved by websocket
	 * @return array
	 * @internal
	 */
	private function raw(): array
	{
		return [
			__FUNCTION__,
			function ($packet): void
			{
				var_dump($packet);
			}
		];
	}

	/**
	 * @return array
	 * @internal
	 */
	protected function guildMemberAdd(): array
	{
		return [
			__FUNCTION__,
			function (\CharlotteDunois\Yasmin\Models\GuildMember $member)
			{
				$member->guild->fetchInvites()->done(function (\CharlotteDunois\Utils\Collection $invites) use ($member)
				{
					$entryFound = false;
					foreach ($invites as $invite) {
						//check which invite usage increment create new instance of invite and store it in database
						if ($invite->uses > $this->client->collector->inviteCache->resolve($invite->guild, $invite->code)->uses) {
							$this->client->emit('guildMemberAddByInvite', $invite->inviter, $member);
							$entryFound = true;
							//cache it in database
							break;
						}
					}

					if(!$entryFound) {
						//check if invite is not cached in collector, if one invite is not then emit guildMemberAddByInvite
					}
				});
			}
		];
	}
}