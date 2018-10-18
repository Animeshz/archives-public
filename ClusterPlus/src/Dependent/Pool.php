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
class Pool extends \CharlotteDunois\Phoebe\Pool
{
	/**
	 * @var \CharlotteDunois\Livia\LiviaClient<\Animeshz\Client>
	 */
	protected $client;

	function __construct(\Animeshz\ClusterPlus\Client $client, array $options)
	{
		parent::__construct($client->loop, $options, $options['worker'], [$client]);
		$this->client = $client;

		$this->on('debug', function ($msg) {
			$this->client->emit('debug', $msg);
		});

		$this->on('message', function (\CharlotteDunois\Phoebe\Worker $worker, \CharlotteDunois\Phoebe\Message $message) {
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
	 * @param \CharlotteDunois\Phoebe\Worker  $worker
	 * @return void
	 */
	function stopWorker(\CharlotteDunois\Phoebe\Worker $worker): void
	{
		$this->emit('debug', 'Stopping worker #'.$worker->id);
		parent::stopWorker($worker);
	}
	
	/**
	 * @return \CharlotteDunois\Phoebe\Worker
	 */
	protected function spawnWorker(): \CharlotteDunois\Phoebe\Worker
	{
		$worker = parent::spawnWorker();
		$this->emit('debug', 'Spawning new worker #'.$worker->id);
		
		return $worker;
	}

}