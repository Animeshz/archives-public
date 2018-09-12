<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

return function(\CharlotteDunois\Yasmin\Client $client) {
	return (new class($client) extends \CharlotteDunois\Livia\Commands\Command {
		function __construct($client) {
			parent::__construct($client, array(
				'name' => 'create-command',
				'group' => 'clusterplus_meta',
				'description' => 'Creates a command',
				'guildOnly' => true,
				'args' => array(
					array(
						'key' => 'actions',
						'prompt' => 'Define some actions',
						'type' => 'string',
						'infinite' => true
					)
				)
			));
		}

		function run(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$message->say(var_export($args['actions'], true));
			foreach ($args['actions'] as $value) {
				
			}
		}
	});
};