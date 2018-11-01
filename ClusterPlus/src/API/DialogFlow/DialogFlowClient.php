<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow;

use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager;
use \Animeshz\ClusterPlus\API\DialogFlow\GoogleTokenHandler;
use \Animeshz\ClusterPlus\Client;
use \CharlotteDunois\Events\EventEmitterInterface;
use \CharlotteDunois\Events\EventEmitterTrait;
use \CharlotteDunois\Yasmin\Models\ClientBase;

/**
 * DialogFlowClient, what you'd expect it to do?
 * Interact with rest api of dialogflow and get objects,
 * store those in this client as Collection.
 * 
 * @see https://github.com/Charlottedunois/Collection
 * @see https://github.com/Charlottedunois/EventEmitter
 */
class DialogFlowClient implements EventEmitterInterface, \Serializable
{
	use EventEmitterTrait;

	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\APIManager
	 */
	protected $api;
	
	/**
	 * @var \Animeshz\ClusterPlus\Client
	 */
	protected $client;
	
	/**
	 * @var \Google\Auth\Credentials\ServiceAccountCredentials<\Google\Auth\CredentialsLoader>
	 */
	protected $credentials;

	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\GoogleTokenHandler
	 */
	protected $tokenHandler;

	/**
	 * Constructor
	 * @param \Animeshz\ClusterPlus\Client $client
	 */
	function __construct(Client $client)
	{
		$this->client = $client;

		\putenv('GOOGLE_APPLICATION_CREDENTIALS='.$client->getOption('dialogflow'));
		$this->credentials = \Google\Auth\ApplicationDefaultCredentials::getCredentials(['https://www.googleapis.com/auth/cloud-platform', 'https://www.googleapis.com/auth/dialogflow']);
		$this->tokenHandler = new GoogleTokenHandler($this);

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
			case 'api':
			case 'APIManager':
			case 'apiManager':
			return $this->api;
			break;

			case 'client':
			return $this->client;
			break;

			case 'credentials':
			return $this->credentials;
			break;

			case 'token':
			case 'tokenHandler':
			return $this->tokenHandler;
			break;
		}
		
		throw new \RuntimeException('Unknown property '.\get_class($this).'::$'.$name);
	}

	function serialize(): ?string
	{
		\Animeshz\ClusterPlus\API\DialogFlow\Models\ClientBase::$serializeClient = $this;
		return null;
	}

	function unserialize($vars): void
	{
		if(ClientBase::$serializeClient === null) {
			throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
		}
		\unserialize($vars);

		$this->client = ClientBase::$serializeClient;
		$this->api = new APIManager($this, $this->client);		
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