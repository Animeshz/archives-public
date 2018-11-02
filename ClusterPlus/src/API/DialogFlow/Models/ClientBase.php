<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

use \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient;

abstract class ClientBase implements \Serializable
{    
	/**
	 * The client which will be used to unserialize.
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient|null
	 */
	public static $serializeClient;

	protected $client;

	function __construct(DialogFlowClient $client)
	{
		$this->client = $client;
	}

	function serialize(): ?string
	{
		$vars = get_object_vars($this);
		unset($vars['client']);
		
		return \serialize($vars);
	}

	function unserialize($vars): void
	{
		if(self::$serializeClient === null) {
            throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
        }

		$vars = \unserialize($vars);
		foreach ($vars as $key => $value) {
			$this->$key = $value;
		}

		$this->client = self::$serializeClient;
	}
}
