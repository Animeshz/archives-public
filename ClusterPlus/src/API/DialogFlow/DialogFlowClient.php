<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow;

use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager;
use \Animeshz\ClusterPlus\API\DialogFlow\Models\Agent;
use \Animeshz\ClusterPlus\API\DialogFlow\Models\Answer;
use \Animeshz\ClusterPlus\API\DialogFlow\Models\AnswerStorage;
use \Animeshz\ClusterPlus\API\DialogFlow\Models\QueryInput;
use \Animeshz\ClusterPlus\API\DialogFlow\Models\TextInput;
use \Animeshz\ClusterPlus\Client;
use \CharlotteDunois\Events\EventEmitterInterface;
use \CharlotteDunois\Events\EventEmitterErrorTrait;
use \CharlotteDunois\Yasmin\Models\ClientBase;
use \React\Promise\ExtendedPromiseInterface;
use \React\Promise\Promise;
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
	use EventEmitterErrorTrait;

	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\APIManager
	 */
	protected $api;

	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\Models\AnswerStorage
	 */
	protected $answers;
	
	/**
	 * @var \Animeshz\ClusterPlus\Client
	 */
	protected $client;
	
	/**
	 * @var \Google\Auth\Credentials\ServiceAccountCredentials<\Google\Auth\CredentialsLoader>
	 */
	protected $credentials;

	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\Models\Agent
	 */
	protected $me;

	/**
	 * @var array
	 */
	protected $project;

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
		if(\Animeshz\ClusterPlus\API\DialogFlow\Models\ClientBase::$serializeDialogflow === null) \Animeshz\ClusterPlus\API\DialogFlow\Models\ClientBase::$serializeDialogflow = $this;

		$this->answers = new AnswerStorage($this);

		\putenv('GOOGLE_APPLICATION_CREDENTIALS='.$client->getOption('dialogflow'));
		$this->project = json_decode(file_get_contents($client->getOption('dialogflow')), true);
		$this->credentials = \Google\Auth\ApplicationDefaultCredentials::getCredentials(['https://www.googleapis.com/auth/cloud-platform', 'https://www.googleapis.com/auth/dialogflow']);

		$this->tokenHandler = new GoogleTokenHandler($this);
		$this->api = new APIManager($this, $client);

		$this->retrieveAgent();
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

			case 'me':
			case 'itself':
			case 'agent':
			return $this->me;
			break;
		}

		if(property_exists($this, $name)) return $this->$name;
		
		throw new \RuntimeException('Unknown property '.\get_class($this).'::$'.$name);
	}

	function serialize(): string
	{
		$vars = get_object_vars($this);
		unset($vars['api'], $vars['client'], $vars['credentials'], $vars['listener'], $vars['onceListener']);
		return \serialize($vars);
	}

	function unserialize($vars): void
	{
		\Animeshz\ClusterPlus\API\DialogFlow\Models\ClientBase::$serializeDialogflow = $this;
		if(ClientBase::$serializeClient === null) throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');

		$vars = \unserialize($vars);
		foreach ($vars as $key => $value) {
			$this->$key = $value;
		}

		$this->credentials = \Google\Auth\ApplicationDefaultCredentials::getCredentials(['https://www.googleapis.com/auth/cloud-platform', 'https://www.googleapis.com/auth/dialogflow']);
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

	/**
	 * Fetches and sets $me property.
	 * @internal
	 * @return void
	 */
	private function retrieveAgent(): void
	{
		$this->api->endpoints->agent->getAgent($this->project['project_id'])->done(function (array $data) {
			$this->me = new Agent($this, $data);
		}, function (\Exception $e) {
			$this->handlePromiseRejection($e);
		});
	}

	/**
	 * Sends request to dialogflow about the $request. Resolves with an instance of Answer.
	 * @param string $request 
	 * @param string $sessionid 
	 * @return \React\Promise\ExtendedPromiseInterface
	 */
	function getAnswer(string $request, string $sessionid): ExtendedPromiseInterface
	{
		return (new Promise(function (callable $resolve, callable $reject) use ($request, $sessionid) {
			$text = new TextInput($request);
			$input = new QueryInput($text);
			$response = $this->api->endpoints->sessions->detectIntent($this->project['project_id'], $sessionid, $input)->then(function ($data) use ($resolve) {
				$answer = $this->answers->factory($data);
				$resolve($answer);
			}, $reject);
		}));
	}
}