<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

use \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient;

class QueryResult extends ClientBase implements \JsonSerializable
{
	/**
	 * @var string
	 */
	protected $queryText;

	/**
	 * @var string
	 */
	protected $languageCode = '';

	/**
	 * @var int
	 */
	protected $speechRecognitionConfidence;

	/**
	 * @var string
	 */
	protected $action;

	/**
	 * @var array
	 */
	protected $parameters;

	/**
	 * @var bool
	 */
	protected $allRequiredParamsPresent;

	/**
	 * @var string
	 */
	protected $fulfillmentText;

	/**
	 * @var Message[]
	 */
	protected $fulfillmentMessages;

	/**
	 * @var string|null
	 */
	protected $webhookSource;

	/**
	 * @var array
	 */
	protected $webhookPayload;

	/**
	 * @var Context[]
	 */
	protected $outputContexts;

	/**
	 * @var Intent[]
	 */
	protected $intent;

	/**
	 * @var int
	 */
	protected $intentDetectionConfidence;

	/**
	 * @var array
	 */
	protected $diagnosticInfo;

	/**
	 * @internal
	 * @param DialogFlowClient $dialogflow
	 * @param array $result
	 */
	function __construct(DialogFlowClient $dialogflow, array $result)
	{
		parent::__construct($dialogflow);
		$this->_patch($result);
	}

	/**
	 * @internal
	 * @return void
	 */
	function _patch(array $result): void
	{
		$this->queryText = $result['queryText'];
		$this->languageCode = $result['languageCode'] ?? 'en';
		$this->speechRecognitionConfidence = $result['speechRecognitionConfidence'] ?? 0;
		$this->action = $result['action'];
		$this->parameters = $result['parameters'] ?? [];
		$this->allRequiredParamsPresent = $result['allRequiredParamsPresent'] ?? false;
		$this->fulfillmentText = $result['fulfillmentText'] ?? '';
		$this->fulfillmentMessages = $result['fulfillmentMessages'] ?? [];
		$this->webhookSource = $result['webhookSource'] ?? null;
		$this->webhookPayload = $result['webhookPayload'] ?? [];
		$this->outputContexts = $result['outputContexts'] ?? [];
		$this->intent = $result['intent'] ?? [];
		$this->intentDetectionConfidence = $result['intentDetectionConfidence'] ?? 0;
		$this->diagnosticInfo = $result['diagnosticInfo'] ?? [];
	}
}