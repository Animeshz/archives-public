<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

use \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient;

abstract class ClientBase implements \Serializable, \JsonSerializable
{
	/**
	 * The client which will be used to unserialize.
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient|null
	 */
	public static $serializeDialogflow;

	protected $dialogflow;

	function __construct(DialogFlowClient $dialogflow)
	{
		$this->dialogflow = $dialogflow;
	}

	function serialize(): ?string
	{
		$vars = get_object_vars($this);
		unset($vars['dialogflow']);
		
		return \serialize($vars);
	}

	function unserialize($vars): void
	{
		if(self::$serializeDialogflow === null) throw new \Exception('Unable to unserialize a class without ClientBase::$serializeDialogflow being set');

		$vars = \unserialize($vars);
		foreach ($vars as $key => $value) {
			$this->$key = $value;
		}

		$this->client = self::$serializeDialogflow;
	}

	function __get($name)
	{
		if(property_exists($this, $name)) return $this->$name;
	}

	function jsonSerialize(): array
	{
		$vars = \get_object_vars($this);
		unset($vars['dialogflow']);
		$vars = \array_filter($vars, function ($value) { return $value !== null; });
		return $vars;
	}
}
