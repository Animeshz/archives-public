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

	function createEntities(string $projectid, EntityType $entityTypeID, string $languageCode, Entity ...$entities)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['batchCreate'], $projectid, $entityTypeID);
		return $this->api->makeRequest('POST', $url, ['languageCode' => $languageCode, 'entities' => $entities]);
	}

	function deleteEntities(string $projectid, EntityType $entityTypeID, string $languageCode, string ...$entityValues)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['batchDelete'], $projectid, $entityTypeID);
		return $this->api->makeRequest('POST', $url, ['languageCode' => $languageCode, 'entityValues' => $entityValues]);
	}

	function updateEntities(string $projectid, EntityType $entityTypeID, string $languageCode, string $updateMask, Entity ...$entities)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['batchUpdate'], $projectid, $entityTypeID);
		return $this->api->makeRequest('POST', $url, ['languageCode' => $languageCode, 'updateMask' => $updateMask, 'entities' => $entities]);
	}
}