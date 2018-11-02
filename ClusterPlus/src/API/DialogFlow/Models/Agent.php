<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

use \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient;

class Agent extends ClientBase implements \JsonSerializable
{
	/**
	 * @var string
	 */
	protected $parent;

	/**
	 * @var string
	 */
	protected $displayName;

	/**
	 * @var string
	 */
	protected $defaultLanguageCode;

	/**
	 * @var string[]
	 */
	protected $supportedLanguageCodes;

	/**
	 * @var string
	 */
	protected $timeZone;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string|null
	 */
	protected $avatarUri;

	/**
	 * @var bool
	 */
	protected $enableLogging;

	/**
	 * Either of MATCH_MODE_UNSPECIFIED, MATCH_MODE_HYBRID, MATCH_MODE_ML_ONLY
	 * @var string
	 */
	protected $matchMode;

	/**
	 * @var int
	 */
	protected $classificationThreshold;

	function __construct(DialogFlowClient $client, array $agent)
	{
		parent::__construct($client);
		$this->_patch($agent);
	}

	function jsonSerialize()
	{
		$vars = \get_object_vars($this);
		$vars = \array_filter($vars, function($value) { return $value !== null; });
		return $vars;
	}

	/**
	 * @internal
	 * @return void
	 */
	function _patch(array $agent): void
	{
		$this->parent = $agent['parent'];
		$this->displayName = $agent['displayName'];
		$this->defaultLanguageCode = $agent['defaultLanguageCode'];
		$this->supportedLanguageCodes = $agent['supportedLanguageCodes'] ?? [];
		$this->timeZone = $agent['timeZone'];
		$this->description = $agent['description'];
		$this->avatarUri = $agent['avatarUri'] ?? null;
		$this->enableLogging = $agent['enableLogging'];
		$this->matchMode = $agent['matchMode'];
		$this->classificationThreshold = $agent['classificationThreshold'];
	}
}