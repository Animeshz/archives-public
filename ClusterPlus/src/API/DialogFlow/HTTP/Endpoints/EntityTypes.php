<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFLow\HTTP\Endpoints;

use \Animeshz\ClusterPlus\API\DialogFlow\APIManager;

final class Agent
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

	function deleteEntities(string $projectid, array $entityTypeNames)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['get'], $projectid);
		return $this->api->makeRequest('POST', $url, ['entityTypeNames' => $entityTypeNames]);
	}

	function exportAgent(string $guildid)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['export'], $guildid);
		return $this->api->makeRequest('POST', $url, array());
	}

	function importAgent(string $guildid)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['import'], $guildid);
		return $this->api->makeRequest('POST', $url, array());
	}

	function restoreAgent(string $guildid)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['restore'], $guildid);
		return $this->api->makeRequest('GET', $url, array());
	}

	function searchAgent(string $guildid)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['search'], $guildid);
		return $this->api->makeRequest('GET', $url, array());
	}

	function trainAgent(string $guildid)
	{
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['train'], $guildid);
		return $this->api->makeRequest('PATCH', $url, array());
	}
}