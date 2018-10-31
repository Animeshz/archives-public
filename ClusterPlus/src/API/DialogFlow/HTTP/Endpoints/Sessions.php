<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFLow\HTTP\Endpoints;

use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager;

final class Sessions
{
	const ENDPOINTS = [
		'deleteContexts' => 'projects/%s/agent/sessions/%s/contexts',
		'detectIntent' => 'projects/%s/agent/sessions/%s:detectIntent',
		'contexts' => [
			'create' => 'projects/%s/agent/sessions/%s/contexts',
			'delete' => 'projects/%s/agent/sessions/%s/contexts/%s',
			'get' => 'projects/%s/agent/sessions/%s/contexts/%s',
			'list' => 'projects/%s/agent/sessions/%s/contexts',
			'patch' => 'projects/%s/agent/sessions/%s/contexts/%s'
		],
		'entityTypes' => [
			'create' => 'projects/%s/agent/sessions/%s/entityTypes',
			'delete' => 'projects/%s/agent/sessions/%s/entityTypes/%s',
			'get' => 'projects/%s/agent/sessions/%s/entityTypes/%s',
			'list' => 'projects/%s/agent/sessions/%s/entityTypes',
			'patch' => 'projects/%s/agent/sessions/%s/entityTypes/%s'
		]
	];

	protected $api;

	function __construct(APIManager $api)
	{
		$this->api = $api;
	}

	function createContext(string $projectid, string $sessionid, Context $context)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['contexts']['create'], $projectid, $sessionid);
		return $this->api->makeRequest('POST', $url, [$context]);
	}

	function createEntityType(string $projectid, string $sessionid, SessionEntityType $entityType)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['entityType']['create'], $projectid, $sessionid);
		return $this->api->makeRequest('POST', $url, [$entityType]);
	}

	function deleteContexts(string $projectid, string $sessionid)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['deleteContexts'], $projectid, $sessionid);
		return $this->api->makeRequest('DELETE', $url, []);
	}

	function deleteContext(string $projectid, string $sessionid, string $contextid)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['contexts']['delete'], $projectid, $sessionid, $contextid);
		return $this->api->makeRequest('DELETE', $url, []);
	}

	function deleteEntityType(string $projectid, string $sessionid, string $entityName)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['entityName']['delete'], $projectid, $sessionid, $entityName);
		return $this->api->makeRequest('DELETE', $url, []);
	}

	function detectIntent(string $projectid, string $sessionid, QueryInput $input)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['detectIntent'], $projectid, $sessionid);
		return $this->api->makeRequest('POST', $url, ['queryInput' => $input]);
	}

	function getContext(string $projectid, string $sessionid, string $contextid)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['contexts']['get'], $projectid, $sessionid, $contextid);
		return $this->api->makeRequest('GET', $url, []);
	}

	function getEntityType(string $projectid, string $sessionid, string $entityName)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['contexts']['get'], $projectid, $sessionid, $entityName);
		return $this->api->makeRequest('GET', $url, []);
	}

	function listContexts(string $projectid, string $sessionid)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['contexts']['list'], $projectid, $sessionid);
		return $this->api->makeRequest('GET', $url, []);
	}

	function listEntityTypes(string $projectid, string $sessionid)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['entityType']['list'], $projectid, $sessionid);
		return $this->api->makeRequest('GET', $url, []);
	}

	function updateContext(string $projectid, string $sessionid, string $contextid, Context $context)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['contexts']['patch'], $projectid, $sessionid, $contextid);
		return $this->api->makeRequest('PATCH', $url, [$context]);
	}

	function updateEntityType(string $projectid, string $sessionid, string $entityName, SessionEntityType $entityType)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['entityType']['patch'], $projectid, $sessionid, $entityName);
		return $this->api->makeRequest('PATCH', $url, [$entityType]);
	}
}