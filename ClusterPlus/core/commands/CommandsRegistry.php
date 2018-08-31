<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace ClusterPlus\commands;

class CommandsRegistry
{
	protected $client;

	public function __construct(\CharlotteDunois\Yasmin\Client $client)
	{
		$this->client = $client;
		var_dump($this->loadClasses());
	}
}