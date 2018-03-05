<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFLow\HTTP;

use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager;
use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\Endpoints\Agent;
use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\Endpoints\EntityTypes;
use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\Endpoints\Intents;
use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\Endpoints\Operations;
use \Animeshz\ClusterPlus\API\DialogFlow\HTTP\Endpoints\Sessions;

final class APIEndpoints
{
	/**
     * HTTP constants.
     * @var array
     * @internal
     */
	const HTTP = [
		'url' => 'https://dialogflow.googleapis.com/',
		'version' => 2
	];

	/**
	 * APIManager
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager
	 */
	protected $api;

	/**
	 * Agent endpoint
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\HTTP\Endpoints\Agent
	 */
	public $agent;

	/**
	 * EntityTypes endpoint
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\HTTP\Endpoints\EntityTypes
	 */
	public $entityTypes;

	/**
	 * Intents endpoint
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\HTTP\Endpoints\Intents
	 */
	public $intents;

	/**
	 * Operations endpoint
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\HTTP\Endpoints\Operations
	 */
	public $operations;

	/**
	 * Sessions endpoint
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\HTTP\Endpoints\Sessions
	 */
	public $sessions;

	/**
	 * Endpoint Constructor
	 * @param \Animeshz\ClusterPlus\API\DialogFlow\HTTP\APIManager $api
	 */
	function __construct(APIManager $api)
	{
		$this->api = $api;

		$this->agent = new Agent($api);
		$this->entityTypes = new EntityTypes($api);
		$this->intents = new Intents($api);
		$this->operations = new Operations($api);
		$this->sessions = new Sessions($api);
	}
	
	/**
	 * Formats Endpoints strings.
	 * @param string  $endpoint
	 * @param string  ...$args
	 * @return string
	 */
	static function format(string $endpoint, ...$args)
	{
		return \sprintf($endpoint, ...$args);
	}
}