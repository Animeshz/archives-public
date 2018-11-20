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
use CharlotteDunois\Yasmin\Models\ClientBase;
use CharlotteDunois\Yasmin\Models\Guild;

/**
 * Command Storage
 */
class Storage extends Collection implements \Serializable
{
	/**
	 * @var \Animeshz\ClusterPlus\Client
	 */
	protected $client;

	function __construct(Client $client)
	{
		$this->client = $client;
	}

	function serialize(): string
	{
		return \serialize($this->data);
	}

	function unserialize($data): void
	{
		if(ClientBase::$serializeClient === null) throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');

		$this->client = ClientBase::$serializeClient;
		$this->data = \unserialize($data);
	}
}