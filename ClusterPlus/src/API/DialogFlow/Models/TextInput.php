<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

class TextInput implements \JsonSerializable
{
	protected $text;

	protected $languageCode = 'en-US';

	function __construct(string $text, ?string $languageCode = null)
	{
		$this->text = $text;

		if($languageCode !== null) $this->languageCode = $languageCode;
	}

	function jsonSerialize()
	{
		$vars = \get_object_vars($this);
		return $vars;
	}
}