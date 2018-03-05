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
		'get' => 'projects/%s/agent',
		'export' => 'projects/%s/agent:export',
		'import' => 'projects/%s/agent:import',
		'restore' => 'projects/%s/agent:restore',
		'search' => 'projects/%s/agent:search',
		'train' => 'projects/%s/agent:export',
	];

	protected $api;

	function __construct(APIManager $api)
	{
		$this->api = $api;
	}

	function getAgent(string $projectid) {
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['get'], $projectid);
		return $this->api->makeRequest('GET', $url, []);
	}

	function exportAgent(string $projectid, string $agentUri) {
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['export'], $projectid);
		return $this->api->makeRequest('POST', $url, ['agentUri' => $agenturi]);
	}

	function importAgent(string $projectid, string $agentUri string $agentContent) {
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['import'], $projectid);
		return $this->api->makeRequest('POST', $url, ['agentUri' => $agenturi, 'agentContent' => $agentContent]);
	}

	function restoreAgent(string $projectid, string $agentUri string $agentContent) {
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['restore'], $projectid);
		return $this->api->makeRequest('POST', $url, ['agentUri' => $agenturi, 'agentContent' => $agentContent]);
	}

	function searchAgent(string $projectid, array $agents, string $nextPageToken) {
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['search'], $projectid);
		return $this->api->makeRequest('GET', $url, ['agents': $agents, 'nextPageToken': $nextPageToken]);
	}

	function trainAgent(string $projectid) {
		$url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['train'], $projectid);
		return $this->api->makeRequest('POST', $url, []);
	}
}