<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFLow\HTTP\Endpoints;

use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager;

final class EntityTypes
{
	const ENDPOINTS = [
		'batchCreate' => 'projects/%s/agent/entityTypes/%s/entities:batchCreate',
		'batchDelete' => 'projects/%s/agent/entityTypes/%s/entities:batchDelete',
		'batchUpdate' => 'projects/%s/agent/entityTypes/%s/entities:batchUpdate'
	];

	protected $api;

	function __construct(APIManager $api)
	{
		$this->api = $api;
	}

	function createEnities(string $projectid, EntityType $entityTypeID, string $languageCode, Entity ...$entities)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['create'], $projectid, $entityTypeID);
		return $this->api->makeRequest('POST', $url, ['languageCode' => $languageCode, 'entities' => $entities]);
	}

	function createEnities(string $projectid, EntityType $entityTypeID, string $languageCode, string ...$entityValues)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['create'], $projectid, $entityTypeID);
		return $this->api->makeRequest('POST', $url, ['languageCode' => $languageCode, 'entityValues' => $entityValues]);
	}

	function createEnities(string $projectid, EntityType $entityTypeID, string $languageCode, string $updateMask, Entity ...$entities)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['create'], $projectid, $entityTypeID);
		return $this->api->makeRequest('POST', $url, ['languageCode' => $languageCode, 'updateMask' => $updateMask, 'entities' => $entities]);
	}
}