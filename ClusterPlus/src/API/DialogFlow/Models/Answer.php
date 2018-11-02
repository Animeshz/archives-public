<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

use \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient;

class Answer extends ClientBase
{
	/**
	 * @var string
	 */
	protected $responseId;

	/**
	 * @var QueryResult
	 */
	protected $queryResult;

	/**
	 * @var Status|null
	 */
	protected $webhookStatus;

	function __construct(DialogFlowClient $dialogflow, array $answer)
	{
		parent::__construct($dialogflow);
		$this->_patch($answer);
	}

	function __toString(): string
	{
		return $this->queryResult->fulfillmentText;
	}

	/**
	 * @internal
	 * @return void
	 */
	function _patch(array $answer): void
	{
		$this->responseId = $answer['responseId'];
		$this->queryResult = isset($answer['queryResult']) ? new QueryResult($this->dialogflow, $answer['queryResult']) : $this->queryResult;
		$this->webhookStatus = isset($answer['webhookStatus']) ? new Status($this->dialogflow, $answer['webhookStatus']) : $this->webhookStatus;
	}
}