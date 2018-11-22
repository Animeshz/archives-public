<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

use Animeshz\ClusterPlus\Client;
use Animeshz\ClusterPlus\Dependent\Command;
use CharlotteDunois\Livia\CommandMessage;
use CharlotteDunois\Yasmin\Models\MessageEmbed;

return function(Client $client) {
	return (new class($client) extends Command {
		function __construct(Client $client) {
			parent::__construct($client, [
				'name' => 'create-command',
				'group' => 'custom',
				'description' => 'Creates a command',
				'guildOnly' => true,
				'userPermissions' => [
					'MANAGE_CHANNELS',
					'MANAGE_GUILD'
				],
				'args' => [
					[
						'key' => 'name',
						'prompt' => 'Name of your command',
						'type' => 'string',
						'validate' => function ($value) {
							if (mb_strlen($value) === 0) {
								return false;
							}

							if (count($this->client->registry->findCommands($value)) > 0) {
								return 'That command name has already been taken by one of the commands in registry.';
							}

							return true;
						}
					],
					[
						'key' => 'description',
						'prompt' => 'Give your command a short description',
						'type' => 'string',
						'infinite' => true
					]
				]
			]);
		}

		function threadRun(CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$guild = $message->message->guild;
			$name = $args['name'];
			$description = implode(' ', $args['description']);

			if ($this->client->collector->commands->resolve($message->message->guild, $name) !== null) {
				return $message->say('', ['embed' => new MessageEmbed(['description' => 'A command of name '.$name.' already exists, Use our android app for more options.'])]);
			}

			try {
				$cmd = new class($this->client, $guild, $name, $description) extends \Animeshz\ClusterPlus\Models\Command {
					function __construct(Client $client, $guild, $name, $description) {
						parent::__construct($client, [
							'name' => $name,
							'description' => $description,
							'guild' => $guild
						]);
					}
				};
				$this->client->collector->setCommands($cmd);
				return $message->say('', ['embed' => new MessageEmbed(['description' => 'Successfully created command. Use our android app to create and attach a module.'])]);
			} catch (\InvalidArgumentException $e) {
				return $message->say('', ['embed' => new MessageEmbed(['description' => 'Sorry but command name must be lower-case and should not have any whitespaces between them'])]);
			}
		}
	});
};