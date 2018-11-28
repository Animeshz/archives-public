<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Dependent;

use Animeshz\ClusterPlus\Client;
use CharlotteDunois\Collect\Collection;
use CharlotteDunois\Yasmin\Models\ClientBase;
use React\MySQL\Factory;
use React\MySQL\ConnectionInterface;
use React\Promise\ExtendedPromiseInterface;

/**
 * Livia MySQLProvider implementation.
 * 
 * To use this provider in threads, you must call threadReady()
 * providing your client's instance with it, then you can use the
 * promise returned to do something with this provider in that thread.
 */
class MySQLProvider extends \CharlotteDunois\Livia\Providers\MySQLProvider implements \Serializable
{
	protected $formdata;

	function __construct(ConnectionInterface $db)
	{
		parent::__construct($db);
		$this->formdata = new Collection();
	}

	function init(\CharlotteDunois\Livia\LiviaClient $client): ExtendedPromiseInterface
	{
		return parent::init($client)->then(function()
		{
			$this->runQuery('CREATE TABLE IF NOT EXISTS `formdata` (`guild` VARCHAR(20) NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`guild`));')->then(function () {
				return $this->runQuery('SELECT * FROM `formdata`;')->then(function ($result) {
					foreach($result->resultRows as $row) {
						$this->loadFormDataRow($row);
					}
					return null;
				});
			});
		});
	}

	function serialize(): string
	{
		$vars = get_object_vars($this);
		unset($vars['client'], $vars['db'], $vars['settings']);
		return \serialize($vars);
	}

	function unserialize($data): void
	{
		$data = \unserialize($data);
		foreach ($data as $key => $value) {
			$this->key = $value;
		}
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