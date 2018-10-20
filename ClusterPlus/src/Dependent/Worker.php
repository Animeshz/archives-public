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

/**
 * Phoebe Worker implementation
 */
class Worker extends \CharlotteDunois\Phoebe\Worker
{
	/**
	 * @var \Animeshz\ClusterPlus\Client
	 */
	protected $internalClient;
	
	/**
	 * The thread-local client. Only set in the worker thread.
	 * @var \Animeshz\ClusterPlus\Client
	 */
	public static $client;
	
	/**
	 * Me. Myself. Only set in the worker thread.
	 * @var \Animeshz\ClusterPlus\Dependent\Worker
	 */
	public static $me;

	/**
	 * Constructor.
	 * @param \Animeshz\ClusterPlus\Client  $client
	 */
	function __construct(Client $client)
	{
		parent::__construct();
		$this->internalClient = $client;
	}

	/**
	 * @return void
	 * @internal
	 */
	function run(): void
	{
		$this->bootstrap();
		
		static::$me = $this;
		Worker::$me = $this;
		
		$client = $this->internalClient;
		$this->internalClient = null;
		
		static::$client = $client;
		static::$loop = $client->getLoop();
		
		$client->on('error', function ($e) {
			$stack = Message::exportException($e);
			$message = new Message('internal-error-handling', $stack);
			$this->sendMessageToPool($message);
		});

		$this->addTimer();
		$this->loop();
	}

}