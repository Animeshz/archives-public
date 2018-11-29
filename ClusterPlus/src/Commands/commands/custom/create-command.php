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
use CharlotteDunois\Yasmin\Models\MessageEmbed;
use React\Promise\ExtendedPromiseInterface;

use function React\Promise\resolve;

return function(Client $client) {
	return (new class($client) extends SarahCommand {
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
						'type' => 'string'
					]
				]
			]);
		}

		/**
		 * @return \React\Promise\ExtendedPromiseInterface
		 */
		function run(CommandMessage $message, \ArrayObject $args, bool $fromPattern) {
			if ($this->client->collector->commands->resolve($message->message->guild, $args['name']) !== null) {
				return $message->say('', ['embed' => new MessageEmbed(['description' => 'A command of name '.$name.' already exists, Use our android app for more options.'])]);
			}

			return $this->client->pool->run($this->name, 'threadRun', $message, $args, $fromPattern)->then(function ($result) use ($message) {
				if($result instanceof \Animeshz\ClusterPlus\Models\Command) {
					$this->client->collector->setCommands([$result], true);
					return $message->say('', ['embed' => new MessageEmbed(['description' => 'Successfully created command. Use our android app to create and attach a module.'])]);
				}
			});
		}

		function threadRun(CommandMessage $message, \ArrayObject $args, bool $fromPattern): ExtendedPromiseInterface
		{
			$guild = $message->message->guild;
			$name = $args['name'];
			$description = $args['description'];

			try {
				$cmd = new \Animeshz\ClusterPlus\Models\Command($this->client, ['guild' => $guild, 'name' => $name, 'description' => $description]);
				return resolve($cmd);
			} catch (InvalidArgumentException $e) {
				return $message->say('', ['embed' => new MessageEmbed(['description' => 'Sorry but command name must be lower-case and should not have any whitespaces between them'])]);
			} catch (Exception $e) {
				$this->client->handlePromiseRejection($e);
				return $message->say('', ['embed' => new MessageEmbed(['description' => 'Oops something went wrong we\'ve cached the problem it\'ll be resolved.'])]);
			}
		}
	});
};