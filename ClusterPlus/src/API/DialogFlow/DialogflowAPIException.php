<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow;

class DialogFlowAPIException extends \Exception
{
	function __construct(string $path, array $errors)
	{
		parent::__construct($errors['error']['status'].': '.$errors['error']['message'], $errors['error']['code']);
	}
}