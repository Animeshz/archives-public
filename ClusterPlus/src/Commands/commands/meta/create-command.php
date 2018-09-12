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
			$e = new \Exception;
			$message->say(var_export($e->getTraceAsString(), true));
			//validate command
			// foreach ($args['actions'] as $action) {
			// 	preg_match("/('.*?')/", $action, $function);
			// 	preg_match("/\([^)]+\)/", $action, $args);

			// 	// $function = \trim($function, "'");
			// 	// $args = trim($args, '()');

			// 	var_dump($action, $function, $args);

			// 	// $argsArr = \trim(\explode(',', $args));

			// 	// if(\method_exists('\\ClusterPlus\\Interfaces\\documentary\\'.$function, $args[0])) {

			// 	// }
			// }

			// var_dump($args['actions']);

			//register command in database
			//create another class for maintaining the command created
		}
	});
};