<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

use Animeshz\ClusterPlus\Client;
use CharlotteDunois\Livia\CommandMessage;
use CharlotteDunois\Sarah\SarahCommand;

return function(Client $client) {
	return (new class($client) extends SarahCommand {
		function __construct($client) {
			parent::__construct($client, [
				'name' => 'create-module',
				'group' => 'custom',
				'description' => 'Creates a module',
				'guildOnly' => true,
			]);
		}

		function threadRun(CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$message->say('Unfortunately modules can only be created using our android app.');
		}
	});
};