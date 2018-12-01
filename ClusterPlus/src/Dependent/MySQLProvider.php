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
use CharlotteDunois\Livia\Providers\SettingProvider;
use CharlotteDunois\Yasmin\Models\ClientBase;
use React\MySQL\Factory;
use React\MySQL\ConnectionInterface;
use React\Promise\ExtendedPromiseInterface;

use Exception;

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
		if(ClientBase::$serializeClient === null) throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
		$this->client = ClientBase::$serializeClient;

		$data = \unserialize($data);
		foreach ($data as $key => $value) {
			$this->key = $value;
		}

		$this->client->loop->futureTick(function () {
			$factory = new Factory($this->client->loop);
			$factory->createConnection($this->client->getOption('database')['user'].':'.$this->client->getOption('database')['pass'].'@'.$this->client->getOption('database')['server'].'/'.$this->client->getOption('database')['db'])->then(function (ConnectionInterface $db)
			{
				$this->db = $db;
			})->then(function () {
				$this->settings = new Collection;
				return $this->init($this->client);
			})->then(function () {
				$this->providerState = SettingProvider::STATE_READY;
			})->otherwise(function (Exception $e) { $this->client->handlePromiseRejection($e); });
		});
	}
}