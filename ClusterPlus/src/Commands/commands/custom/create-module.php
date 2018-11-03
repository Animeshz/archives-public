<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

return function(\Animeshz\ClusterPlus\Client $client) {
	return (new class($client) extends \Animeshz\ClusterPlus\Dependent\Command {
		function __construct($client) {
			parent::__construct($client, [
				'name' => 'create-module',
				'group' => 'custom',
				'description' => 'Creates a module',
				'guildOnly' => true,
				'args' => [
					// [
					// 	'key' => 'actions',
					// 	'prompt' => 'Define some actions',
					// 	'type' => 'string',
					// 	'infinite' => true
					// ]
				]
			]);
		}

		function threadRun(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			
		}
	});
};