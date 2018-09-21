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
						'key' => 'name',
						'prompt' => 'Name of your command',
						'type' => 'string'
					),
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
			// validate command
			foreach ($args['actions'] as $attach) {
				preg_match("/\([^)]+\)/i", $attach, $arguments);
				$function = \preg_replace("/\([^)]+\)/", "", $attach);
				$arguments = \explode(',', \trim($arguments[0], '()'));
				$action = \trim(\array_shift($arguments));

				var_dump($function, $action, $arguments);

				foreach ($arguments as $k => $argument) {
					if(1 !== preg_match('[.]')) continue;
					$parts = \explode('.', $argument);
					unset($arguments[$k]);

					//use the value as parameter to next
				}

				// if(\method_exists('\\ClusterPlus\\Interfaces\\documentary\\'.$function, $args[0]))
			}

			//register command in database
			//create another class for maintaining the command created
		}
	});
};