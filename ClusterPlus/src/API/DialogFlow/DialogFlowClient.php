<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFLow;

use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager;
use \Animeshz\ClusterPlus\Client;
use \CharlotteDunois\Events\EventEmitterInterface;
use \CharlotteDunois\Events\EventEmitterTrait;

/**
 * DialogFlowClient, what you'd expect it to do?
 * Interact with rest api of dialogflow and get objects,
 * store those in this client as Collection.
 * 
 * @see https://github.com/Charlottedunois/Collection
 * @see https://github.com/Charlottedunois/EventEmitter
 */
class DialogFlowClient implements EventEmitterInterface
{
	use EventEmitterTrait;

	/**
	 * @var \Animeshz\ClusterPlus\Client
	 */
	protected $client;
	
	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\APIManager
	 */
	protected $api;

	/**
	 * Constructor
	 * @param \Animeshz\ClusterPlus\Client $client
	 */
	function __construct(Client $client)
	{
		$this->client = $client;

		$this->api = new APIManager($this, $client);
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
		switch ($name) {
			case 'client':
			return $this->client;
			break;
			
			case 'api':
			case 'APIManager':
			case 'apiManager':
			return $this->api;
			break;
		}
		
		throw new \RuntimeException('Unknown property '.\get_class($this).'::$'.$name);
	}

	/**
     * Emits an error for an unhandled promise rejection.
     * @return void
     * @internal
     */
	function handlePromiseRejection($error)
	{
		$this->emit('error', $error);
	}

	function getAnswer(string $question)
	{
		//create text instance
		//set text instance to query
		//send
	}
}