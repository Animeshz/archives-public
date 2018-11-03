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
use \React\MySQL\Factory;
use \React\MySQL\ConnectionInterface;

/**
 * Livia MySQLProvider implementation
 */
class MySQLProvider extends \CharlotteDunois\Livia\Providers\MySQLProvider implements \Serializable
{
	function serialize(): string
	{
		$vars = get_object_vars($this);
		unset($vars['client'], $vars['db']);
		return \serialize($vars);
	}

	function unserialize($data): void
	{
		if(ClientBase::$serializeClient === null) {
			throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
		}

		$data = \unserialize($data);
		foreach ($data as $key => $value) {
			$this->key = $value;
		}
		$this->client = ClientBase::$serializeClient;
		$this->client->loop->futureTick(function ()
		{
			$factory = new Factory($this->client->loop);
			$factory->createConnection($this->client->getOption('database')['user'].':'.$this->client->getOption('database')['pass'].'@'.$this->client->getOption('database')['server'].'/'.$this->client->getOption('database')['db'])->done(function (ConnectionInterface $db)
			{
				$this->db = $db;
			});
		});
	}
}