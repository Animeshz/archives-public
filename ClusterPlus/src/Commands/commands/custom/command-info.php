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
				'name' => 'command-info',
				'group' => 'custom',
				'description' => 'See commands infos which are created by create-command',
				'guildOnly' => true
			]);
		}

		function run(CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$guild = $message->message->guild;

			$output = 'To run a command here, use '.Command::anyUsage('command', $this->client->getGuildPrefix($message->message->guild), $this->client->user).\PHP_EOL.\PHP_EOL;
			$this->client->collector->commands->get($guild->id)->each(function ($command) use (&$output) {
				$output .= '**'.$command->name.'**'.': '.$command->description;
			});
			$message->say($output);
		}
	});
};