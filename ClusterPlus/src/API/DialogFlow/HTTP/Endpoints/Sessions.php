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
		'detectIntent' => 'projects/%s/agent/sessions/%s:detectIntent'
	];

	protected $api;

	function __construct(APIManager $api)
	{
		$this->api = $api;
	}

	function deleteContexts(string $projectid, string $sessionid)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['export'], $projectid, $sessionid);
		return $this->api->makeRequest('DELETE', $url, []);
	}

	function detectIntent(string $projectid, string $sessionid, QueryInput $input)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['export'], $projectid, $sessionid);
		return $this->api->makeRequest('POST', $url, ['queryInput' => $input]);
	}
}