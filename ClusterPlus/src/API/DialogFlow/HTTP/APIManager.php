<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFLow\HTTP;

use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIEndpoints;
use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIRequest;
use \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient;
use \Animeshz\ClusterPlus\Client;
use \CharlotteDunois\Yasmin\Utils\URLHelpers;
use \React\Promise\Deferred;
use \React\Promise\Promise;
use \React\Promise\ExtendedPromiseInterface;

class APIManager
{
	/**
	 * @var \Animeshz\ClusterPlus\Client
	 */
	protected $client;

	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient
	 */
	protected $dialogflow;
	
	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIEndpoints
	 */
	protected $endpoints;

	/**
	 * Are we globally ratelimited?
	 * @var bool
	 */
	protected $limited = false;

	/**
	 * @var \React\EventLoop\LoopInterface
	 */
	protected $loop;

	/**
	 * When can we send again?
	 * @var float
	 */
	protected $remaining = 180;
	
	/**
	 * The queue for our API requests.
	 * @var array
	 */
	protected $queue = array();

	/**
	 * API Manager constructor
	 * @param \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient		$dialogflow 
	 * @param \Animeshz\ClusterPlus\Client								$client
	 */
	function __construct(DialogFlowClient $dialogflow, Client $client)
	{
		$this->dialogflow = $dialogflow;
		$this->client = $client;
		$this->loop = $client->loop;
		$this->endpoints = new APIEndpoints($this);

		$client->addPeriodicTimer(60, function () {
			$this->limited = false;
			$this->remaining = 180;
			$this->process();
		});
	}

	/**
	 * Default destructor.
	 * @internal
	 */
	function __destruct()
	{
		$this->clear();
	}
	
	/**
	 * @param string  $name
	 * @return bool
	 * @throws \Exception
	 * @internal
	 */
	function __isset($name)
	{
		try {
			return $this->$name !== null;
		} catch (\RuntimeException $e) {
			if($e->getTrace()[0]['function'] === '__get') {
				return false;
			}
			
			throw $e;
		}
	}
	
	/**
	 * @param string  $name
	 * @return mixed
	 * @throws \RuntimeException
	 * @internal
	 */
	function __get($name)
	{
		switch($name) {
			case 'client':
			return $this->client;
			break;
			case 'endpoints':
			return $this->endpoints;
			break;
			case 'dialogflow':
			return $this->dialogflow;
			break;
		}
		
		throw new \RuntimeException('Unknown property '.\get_class($this).'::$'.$name);
	}

	/**
	 * Adds an APIRequest to the queue.
	 * @param \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIRequest  $apirequest
	 * @return \React\Promise\ExtendedPromiseInterface
	 */
	function add(APIRequest $apirequest): ExtendedPromiseInterface
	{
		return (new Promise(function (callable $resolve, callable $reject) use ($apirequest) {
			$apirequest->deferred = new Deferred();
			$apirequest->deferred->promise()->done($resolve, $reject);
			
			$this->dialogflow->emit('debug', 'Adding request "'.$apirequest->getEndpoint().'" to global queue');
			$this->queue[] = $apirequest;

			$this->processFuture();
		}));
	}
	
	/**
	 * Clears all the queue.
	 * @return void
	 */
	function clear(): void
	{
		$this->limited = true;
		$this->resetTime = \INF;
		
		while($item = \array_shift($this->queue)) {
		    unset($item);
		}
		
		$this->limited = false;
		$this->resetTime = 0;
	}

	/**
	 * Executes an API Request.
	 * @param \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIRequest	$item
	 * @return void
	 */
	protected function execute(APIRequest $item): void
	{        
		$this->dialogflow->emit('debug', 'Executing item "'.$item->getEndpoint().'"');
		$this->remaining--;

		$item->execute()->then(function ($data) use ($item) {
			if($data === 0) {
				$item->deferred->resolve();
			} elseif($data !== -1) {
				$item->deferred->resolve($data);
			}
		}, function ($error) use ($item) {
			$item->deferred->reject($error);
		})->otherwise(function ($error) {
			$this->dialogflow->handlePromiseRejection($error);
		})->done(function (){
			$this->process();
		});
	}

	/**
	 * Makes an API request.
	 * @param string  $method
	 * @param string  $endpoint
	 * @param array   $options
	 * @return \React\Promise\ExtendedPromiseInterface
	 */
	function makeRequest(string $method, string $endpoint, array $data, ?array $options = null): ExtendedPromiseInterface
	{
		if($options === null) $options = [];
		$options['data'] = $data;
		
		$request = new APIRequest($this, $method, $endpoint, $options);
		return $this->add($request);
	}

	/**
	 * Makes an API request synchronously.
	 * @param string  $method
	 * @param string  $endpoint
	 * @param array   $options
	 * @return \React\Promise\ExtendedPromiseInterface
	 */
	function makeRequestSync(string $method, string $endpoint, array $options): ExtendedPromiseInterface
	{
		$apirequest = new APIRequest($this, $method, $endpoint, $options);

		return (new Promise(function (callable $resolve, callable $reject) use ($apirequest) {
			try {
				$request = $apirequest->request();
				$response = URLHelpers::makeRequestSync($request, $request->requestOptions);

				$status = $response->getStatusCode();
				$body = APIRequest::decodeBody($response);

				if($status >= 300) {
					$error = new \RuntimeException($response->getReasonPhrase());
					return $reject($error);
				}

				$resolve($body);
			} catch (\Throwable $e) {
				$reject($e);
			}
		}));
	}

	/**
	 * Processes the queue.
	 * @return void
	 */
	protected function process(): void
	{
		if($this->remaining === 0) $this->limited = true;

		if($this->limited || (\count($this->queue) === 0)) {
			return;
		}

		$item = \array_shift($this->queue);
		$this->processItem($item);
	}

	/**
	 * Processes the queue on future tick.
	 * @return void
	 */
	final protected function processFuture(): void
	{
		$this->loop->futureTick(function () {
			$this->process();
		});
	}

	/**
	 * Processes a queue item.
	 * @param \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIRequest  $item
	 * @return void
	 */
	protected function processItem(APIRequest $item): void
	{
		$this->execute($item);
	}

    /**
     * Unshifts an item into the queue.
     * @param \CharlotteDunois\Yasmin\HTTP\APIRequest  $item
     * @return void
     */
    function unshiftQueue(APIRequest $item): void
    {
        \array_unshift($this->queue, $item);
    }
}