<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFLow\HTTP\Endpoints;

use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager;
use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIEndpoints;

final class Intents
{
	const ENDPOINTS = [
		'batchDelete' => 'projects/%s/agent/intents:batchDelete',
		'batchUpdate' => 'projects/%s/agent/intents:batchUpdate',
		'create' => 'projects/%s/agent/intents',
		'delete' => 'projects/%s/agent/intents/%s',
		'get' => 'projects/%s/agent/intents/%s',
		'list' => 'projects/%s/agent/intents',
		'patch' => 'projects/%s/agent/intents/%s'
	];

	protected $api;

	function __construct(APIManager $api)
	{
		$this->api = $api;
	}

	function createIntent(string $projectid, Intent $intent)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['create'], $projectid);
		return $this->api->makeRequest('POST', $url, [$intent]);
	}

	function deleteIntents(string $projectid, Intent ...$intents)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['batchDelete'], $projectid);
		return $this->api->makeRequest('POST', $url, ['intents' => $intents]);
	}

	function deleteIntent(string $projectid, string $intentID)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['delete'], $projectid, $intentID);
		return $this->api->makeRequest('DELETE', $url, []);
	}

	function getIntent(string $projectid, string $intentID)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['get'], $projectid, $intentID);
		return $this->api->makeRequest('GET', $url, []);
	}

	function listIntents(string $projectid)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['list'], $projectid);
		return $this->api->makeRequest('GET', $url, []);
	}

	function updateIntents(string $projectid, string $languageCode, string $updateMask, string $intentBatchUri, IntentBatch ...$intentBatch)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['batchUpdate'], $projectid);
		return $this->api->makeRequest('POST', $url, ['languageCode' => $languageCode, 'updateMask' => $updateMask, 'intentBatchUri' => $entityBatchUri, 'intentBatchInline' => $intentBatch]);
	}

	function updateIntent(string $projectid, string $intentID, Intent $intent)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['patch'], $projectid, $intentID);
		return $this->api->makeRequest('PATCH', $url, [$intent]);
	}
}