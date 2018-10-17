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
			parent::__construct($client, [
				'name' => 'create-command',
				'group' => 'meta',
				'description' => 'Creates a command',
				'guildOnly' => true,
				'args' => [
					[
						'key' => 'name',
						'prompt' => 'Name of your command',
						'type' => 'string',
						'validate' => function ($value) {
							if(\mb_strlen($value) === 0) {
								return false;
							}

							if(\count($this->client->registry->findCommands($value)) > 0) {
								return 'That command name has already been taken by one of the commands in registry.';
							}

                            //add check for our command registry

							return true;
						}
					],
					[
						'key' => 'description',
						'prompt' => 'Name of your command',
						'type' => 'string',
						'infinite' => true
					]
				]
			]);
		}

		function run(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			global $collector;
			$guild = $message->message->guild;
			$name = $args['name'];
			$description = \implode(' ', $args['description']);

			//create another class for maintaining the command created
			try {
				$cmd = new class($this->client, $guild, $name, $description) extends \Animeshz\ClusterPlus\Models\Command {
					function __construct(\CharlotteDunois\Yasmin\Client $client, $guild, $name, $description) {
						parent::__construct($client, [
							'name' => $name,
							'description' => $description,
							'guild' => $guild
						]);
					}
				};
			} catch (\InvalidArgumentException $e) {
				return $message->say('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['description' => 'Sorry but command name must be lower-case and should not have any whitespaces between them'])]);
			}

			//register command in database
			$commands = $this->client->provider->get($guild, 'commands', []);
			foreach ($commands as $command)
			{
				if($command->name === $cmd->name && $command->guild === $cmd->guild) return $message->say('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['description' => 'Sorry but command with this name is already registered, use command-update or command-delete'])]);
			}
			$commands[] = $cmd;
			$this->client->provider->set($message->message->guild, 'commands', $commands);
			//set to a local cache
			$collector->setCommands($cmd);
			return $message->say('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['description' => 'Successfully created command, use create-mudule to create a new module and then use attach-module to attach any module to this command'])]);
		}
	});
};