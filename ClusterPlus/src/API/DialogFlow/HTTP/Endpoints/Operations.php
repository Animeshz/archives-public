<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFLow\HTTP\Endpoints;

use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager;

final class Operations
{
	const ENDPOINTS = [
		'get' => 'projects/%s/operations/%s'
	];

	protected $api;

	function __construct(APIManager $api)
	{
		$this->api = $api;
	}

	function getOperation(string $projectid, string $operation)
	{
		$url = APIEndpoints::format(self::ENDPOINTS['contexts']['create'], $projectid, $operation);
		return $this->api->makeRequest('GET', $url, []);
	}
}