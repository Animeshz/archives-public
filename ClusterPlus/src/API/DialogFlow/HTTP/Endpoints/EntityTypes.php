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
		'batchDelete' => 'projects/%s/agent/entityTypes:batchDelete',
		'batchUpdate' => 'projects/%s/agent/entityTypes:batchUpdate',
		'create' => 'projects/%s/agent/entityTypes',
		'delete' => 'projects/%s/agent/entityTypes/%s',
		'get' => 'projects/%s/agent/entityTypes/%s',
		'list' => 'projects/%s/agent/entityTypes',
		'patch' => 'projects/%s/agent/entityTypes/%s'
	];

	protected $api;

	function __construct(APIManager $api)
	{
		$this->api = $api;
	}

	function createEnitity(string $projectid, EntityType $entityType)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['create'], $projectid);
		return $this->api->makeRequest('POST', $url, [$entityType]);
	}

	function deleteEntities(string $projectid, string ...$entityTypeNames)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['batchDelete'], $projectid);
		return $this->api->makeRequest('POST', $url, ['entityTypeNames' => $entityTypeNames]);
	}

	function deleteEntity(string $projectid, string $entityTypeID)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['delete'], $projectid, $entityTypeID);
		return $this->api->makeRequest('DELETE', $url, []);
	}

	function getEntity(string $projectid, string $entityTypeID)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['get'], $projectid, $entityTypeID);
		return $this->api->makeRequest('GET', $url, []);
	}

	function listEnitities(string $projectid)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['list'], $projectid);
		return $this->api->makeRequest('GET', $url, []);
	}

	function updateEntities(string $projectid, string $languageCode, string $updateMask, string $entityBatchUri, entityTypeBatch ...$entityTypeBatch)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['batchUpdate'], $projectid);
		return $this->api->makeRequest('POST', $url, ['languageCode' => $languageCode, 'updateMask' => $updateMask, 'entityBatchUri' => $entityBatchUri, 'entityTypeBatchInline' => $entityTypeBatch]);
	}

	function updateEntity(string $projectid, string $entityTypeID)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['patch'], $projectid, $entityTypeID);
		return $this->api->makeRequest('PATCH', $url, [$entityType]);
	}
}