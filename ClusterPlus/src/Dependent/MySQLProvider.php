<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Dependent;

use \Animeshz\ClusterPlus\Client;
use \CharlotteDunois\Collect\Collection;
use \CharlotteDunois\Yasmin\Models\ClientBase;
use \React\MySQL\Factory;
use \React\MySQL\ConnectionInterface;
use \React\Promise\ExtendedPromiseInterface;

/**
 * Livia MySQLProvider implementation
 */
class MySQLProvider extends \CharlotteDunois\Livia\Providers\MySQLProvider implements \Serializable
{
	function __get($name)
	{
		if(property_exists($this, $name)) return $this->$name;
	}

	function serialize(): string
	{
		$vars = get_object_vars($this);
		unset($vars['client'], $vars['db']);
		return \serialize($vars);
	}

	function unserialize($data): void
	{
		$data = \unserialize($data);
		foreach ($data as $key => $value) {
			$this->key = $value;
		}
		// $this->client = ClientBase::$serializeClient;
		// $this->client->loop->futureTick(function ()
		// {
		// 	$factory = new Factory($this->client->loop);
		// 	$factory->createConnection($this->client->getOption('database')['user'].':'.$this->client->getOption('database')['pass'].'@'.$this->client->getOption('database')['server'].'/'.$this->client->getOption('database')['db'])->done(function (ConnectionInterface $db)
		// 	{
		// 		$this->db = $db;
		// 	});
		// });
	}

	function isReady(): bool
	{
		// var_dump($this->db);
		echo \Animeshz\ClusterPlus\Utils\TVarDumper::dump($this->db);
		return false;
		// return \isset($this->db);
	}

	function threadReady(Client $client): ExtendedPromiseInterface
	{
		$this->client = $client;
		$factory = new Factory($client->loop);
		return $factory->createConnection($client->getOption('database')['user'].':'.$client->getOption('database')['pass'].'@'.$client->getOption('database')['server'].'/'.$client->getOption('database')['db'])->then(function (ConnectionInterface $db)
		{
			$this->db = $db;
		})->then(function () use ($client) {
			$this->settings = new Collection;
			return $this->init($client);
		})->otherwise(function (\Throwable $err) use ($client) {
			$client->handlePromiseRejection($err);
		});
	}
}