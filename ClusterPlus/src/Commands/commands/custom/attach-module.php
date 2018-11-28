<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

use Animeshz\ClusterPlus\Client;
use Animeshz\ClusterPlus\Dependent\Command;
use Animeshz\ClusterPlus\Exceptions\MultipleEntryFoundException;
use Animeshz\ClusterPlus\Models\Module;
use CharlotteDunois\Livia\CommandMessage;
use CharlotteDunois\Yasmin\Models\MessageEmbed;

return function(Client $client) {
	return (new class($client) extends Command {
		function __construct($client) {
			parent::__construct($client, [
				'name' => 'attach-module',
				'group' => 'custom',
				'description' => 'Attaches a module to any timer or command',
				'guildOnly' => true,
				'args' => [
					[
						'key' => 'module',
						'prompt' => 'Name of your module you want to attach to',
						'type' => 'string'
					]
				]
			]);
		}

		/**
		 * @return \React\Promise\ExtendedPromiseInterface
		 */
		function run(CommandMessage $message, \ArrayObject $args, bool $fromPattern) {
			try {
				$module = $this->client->collector->modules->resolve($message->message->guild, $args['module']);
				if ($module === null) {
					return $message->say('', ['embed' => new MessageEmbed(['description' => 'Module not found'])]);
				}

				$args['module'] = $module;
			} catch (MultipleEntryFoundException $e) {
				return $message->say('' ['embed' => new MessageEmbed(['description' => $e->getMessage()])]);
			}

			return $this->client->pool->runCommand($this->name, 'threadRun', $message, $args, $fromPattern);
		}

		function threadRun(CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$module = $args['module'];
			$attachableTo = Module::ATTACHABLE_TO;

			$attachments = '';
			for ($i = 0; $i<\count($attachableTo); $i++) {
				$attachments .= ($i+1).' '.$attachableTo[$i].\PHP_EOL;
			}
			$message->say('', ['embed' => new MessageEmbed(['description' => 'Where do you want to attach this module'.\PHP_EOL.$attachments])]);


			$selected = [];
			$listener = function (\CharlotteDunois\Yasmin\Models\Message $msg) use ($attachableTo, $message, $module, &$selected, &$listener)
			{
				if ($msg->channel === $message->message->channel && $msg->author === $message->message->author) {
					if (empty($selected)) {
						$select = (int) $msg->content;
						$count = \count($attachableTo);

						if (!($select <= $count)) return $msg->channel->send('Wrong Option choose between 1 and '.$count);
						$selected['option'] = $attachableTo[($select-1)];
						$prompt = \constant('\Animeshz\ClusterPlusModels\Module::'.\strtoupper($selected['option']));

						$message->say('', ['embed' => new MessageEmbed(['description' => $prompt])]);
					} else {
						$input = $msg->content;

						if ($selected['option'] !== 'Command') {
							if(mb_strpos($selected['option'], 'Event')) {
								list((int)$time, $event) = explode(', ', $input);

								[$this->client, 'add'.$selected['option']]($time, function () use ($module) { $module->runByTimer(); }, $event);
							} else {
								$time = (int) $input;
								[$this->client, 'add'.$selected['option']]($time, function () use ($module) { $module->runByTimer(); });
							}
						} else {
							try {
								$command = $this->client->collector->resolve($command);
							} catch (MultipleEntryFoundException $e) {
								return $msg->channel->send('', ['embed' => new MessageEmbed(['description' => $e->getMessage()])]);
							}

							$command->attachModules([$module]);
						}
					}
				}
			};
			$this->client->on('message', $listener);
		}
	});
};