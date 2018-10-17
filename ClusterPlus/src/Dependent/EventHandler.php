<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Dependent;

/**
 * Attaches listener to the client
 *
 * @property \CharlotteDunois\Livia\Client<\Animeshz\Client>   $client   Instance of current client.
 */
class EventHandler implements \Animeshz\ClusterPlus\Interfaces\EventHandler
{
	/**
	 * @var \CharlotteDunois\Livia\LiviaClient<\Animeshz\Client>
	 */
	protected $client;

	/**
	 * @var array
	 */
	protected $events;

	public function __construct(\Animeshz\ClusterPlus\Client $client, array $excludeFuncs = null)
	{
		$this->client = $client;

		$magic = [
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
			'dispatch'
		];
		if($excludeFuncs !== null) array_merge($magic, $excludeFuncs);

		foreach (get_class_methods($this) as $event) {
			if(in_array($event, $magic)){ continue; }
			$e = $this->$event();
			if(is_array($e) && is_callable($e[1])) {
				$this->events[] = $e;
			}
		}
	}

	protected function ready()
	{
		return [
			__FUNCTION__,
			function ()
			{
				echo 'Logged in as '.$this->client->user->tag.' created on '.$this->client->user->createdAt->format('d.m.Y H:i:s').PHP_EOL;
			}
		];
	}

	public function dispatch()
	{
		foreach ($this->events as $event) {
			$this->client->on($event[0], $event[1]);
		}
	}

	public function debug()
	{
		// return [
		// 	__FUNCTION__,
		// 	function ($debug)
		// 	{
		// 		echo $debug.\PHP_EOL;
		// 	}
		// ];
	}

	protected function guildMemberAdd()
	{
		return [
			__FUNCTION__,
			function (\CharlotteDunois\Yasmin\Models\GuildMember $member)
			{
				$member->guild->fetchInvites()->done(function (\CharlotteDunois\Utils\Collection $invites)
				{
					$invites->each(function (\CharlotteDunois\Yasmin\Models\Invite $invite)
					{
						//check which invite usage increment create new instance of invite and store it in database
						// if($invite->uses === $inv
					});
				});
			}
		];
	}
}