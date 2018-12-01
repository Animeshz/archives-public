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

use Exception;
use function serialize;
use function unserialize;

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
		$vars = get_object_vars($this);
		unset($vars['client']);

		return serialize($vars);
	}

	function unserialize($data): void
	{
		if(ClientBase::$serializeClient === null) throw new Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');

		$vars = unserialize($data);
		foreach ($vars as $key => $value) {
			$this->$key = $value;
		}

		$this->client = ClientBase::$serializeClient;
	}
}