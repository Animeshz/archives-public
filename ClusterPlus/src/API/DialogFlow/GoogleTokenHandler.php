<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow;

use \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient;
use \Animeshz\ClusterPlus\API\DialogFlow\Models\ClientBase;

/**
 * DialogFlowClient, what you'd expect it to do?
 * Interact with rest api of dialogflow and get objects,
 * store those in this client as Collection.
 * 
 * @see https://github.com/Charlottedunois/Collection
 * @see https://github.com/Charlottedunois/EventEmitter
 */
class GoogleTokenHandler implements \Serializable
{
	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient
	 */
	protected $dialogflow;

	/**
	 * @var bool
	 */
	protected $expired = true;

	/**
	 * @var string
	 */
	protected $token;

	/**
	 * @var string
	 */
	protected $tokenType;

	function __construct(DialogFlowClient $dialogflow)
	{
		$this->dialogflow = $dialogflow;
	}

	function __get($name)
	{
		if(property_exists($this, $name)) return $this->$name;
	}

	function serialize(): string
	{
		$vars = get_object_vars($this);
		unset($vars['dialogflow']);
		return \serialize($vars);
	}

	function unserialize($vars): void
	{
		if(ClientBase::$serializeClient === null) {
			throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set in DialogFlow');
		}

		$vars = \unserialize($vars);
		foreach ($vars as $key => $value) {
			$this->key = $value;
		}
		$this->dialogflow = ClientBase::$serializeClient;
	}

	function getToken()
	{
		if($this->expired) $this->generateToken();

		return $this->token;
	}

	function getTokenType()
	{
		if($this->expired) $this->generateToken();

		return $this->tokenType;
	}

	protected function generateToken()
	{
		$token = $this->dialogflow->credentials->fetchAuthToken();
		$this->token = $token['access_token'];
		$this->tokenType = $token['token_type'];
		$this->expired = false;
		$this->dialogflow->client->addTimer($token['expires_in'], function () {
			$this->expired = true;
		});
	}
}