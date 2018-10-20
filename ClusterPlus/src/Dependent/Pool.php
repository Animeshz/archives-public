<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Dependent;

use \Animeshz\ClusterPlus\Client;
use \CharlotteDunois\Phoebe\Message;
use \CharlotteDunois\Phoebe\Worker;

/**
 * Phoebe Pool implementation
 */
class Pool extends \CharlotteDunois\Phoebe\Pool
{
	/**
	 * @var \Animeshz\ClusterPlus\Client<\CharlotteDunois\Livia\LiviaClient>
	 */
	protected $client;

	/**
	 * Constructor.
	 * @param \Animeshz\ClusterPlus\Client		$client		Client who initiated application
	 * @param array								$options	Options with worker
	 */
	function __construct(Client $client, array $options)
	{
		parent::__construct($client->loop, $options, $options['worker'], [$client]);
		$this->client = $client;

		$this->on('debug', function ($msg) {
			$this->client->emit('debug', $msg);
		});

		$this->on('message', function (Worker $worker, Message $message) {
			switch($message->getType()) {
				case 'message-eval-code':
				$payload = $message->getPayload();

				$this->client->eval($payload['code'], ($payload['options'] ?? array()))->done(function ($result) use (&$worker, &$message) {
					return $worker->sendMessageToWorker($message->reply($result));
				}, function ($error) use (&$worker, &$message) {
					return $worker->sendMessageToWorker($message->reply($error));
				});
				break;
			}
		});

		$this->client->once('ready', function () {
			$worker = $this->spawnWorker();
			$this->freeWorkers->enqueue($worker);
		});
	}

	/**
	 * {@inheritdoc}
	 * @param \Animeshz\ClusterPlus\Dependent\Worker  $worker
	 * @return void
	 */
	function stopWorker(Worker $worker): void
	{
		$this->emit('debug', 'Stopping worker #'.$worker->id);
		parent::stopWorker($worker);
	}
	
	/**
	 * @return \Animeshz\ClusterPlus\Dependent\Worker
	 */
	protected function spawnWorker(): Worker
	{
		$worker = parent::spawnWorker();
		$this->emit('debug', 'Spawning new worker #'.$worker->id);
		
		return $worker;
	}

}