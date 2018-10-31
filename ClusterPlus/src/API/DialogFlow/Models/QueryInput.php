<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

class QueryInput implements \JsonSerializable
{
	protected $text;

	protected $event;

	function __construct($input)
	{
		if($input instanceof TextInput) $this->text = $input;
		if($input instanceof EventInput) $this->event = $input;
	}

	function jsonSerialize()
	{
		$vars = \get_object_vars($this);
		$vars = \array_filter($vars, function($value) { return $value !== null; });
		return $vars;
	}
}