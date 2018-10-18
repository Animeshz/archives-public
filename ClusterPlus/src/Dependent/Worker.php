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
class Worker extends \CharlotteDunois\Phoebe\Worker
{
	/**
	 * @var \Animeshz\ClusterPlus\Client
	 */
	protected $internalClient;
    
    /**
     * The thread-local client. Only set in the worker thread.
     * @var \CharlotteDunois\Sarah\SarahClient
     */
    public static $client;
    
    /**
     * Me. Myself. Only set in the worker thread.
     * @var \CharlotteDunois\Sarah\SarahWorker
     */
    public static $me;

	/**
     * Constructor.
     * @param \CharlotteDunois\Sarah\SarahClient  $client
     */
	function __construct(\Animeshz\ClusterPlus\Client $client) {
		parent::__construct();
		$this->internalClient = $client;
	}

	/**
     * @return void
     * @internal
     */
    function run() {
        $this->bootstrap();
        
        static::$me = $this;
        \CharlotteDunois\Phoebe\Worker::$me = $this;
        
        $client = $this->internalClient;
        $this->internalClient = null;
        
        static::$client = $client;
        static::$loop = $client->getLoop();
        
        $client->on('error', function ($e) {
            $stack = \CharlotteDunois\Phoebe\Message::exportException($e);
            $message = new \CharlotteDunois\Phoebe\Message('internal-error-handling', $stack);
            $this->sendMessageToPool($message);
        });

        $this->addTimer();
        $this->loop();
    }

}