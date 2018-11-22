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
class GoogleTokenHandler extends ClientBase implements \Serializable
{
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
		parent::__construct($dialogflow);
	}

	function getToken()
	{
		if ($this->isExpired()) $this->generateToken();

		return $this->token;
	}

	function getTokenType()
	{
		if ($this->isExpired()) $this->generateToken();

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

	function isExpired()
	{
		return $this->expired;
	}
}