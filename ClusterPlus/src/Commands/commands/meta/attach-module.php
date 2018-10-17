<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

return function(\Animeshz\ClusterPlus\Client $client) {
	return (new class($client) extends \CharlotteDunois\Livia\Commands\Command {
		function __construct($client) {
			parent::__construct($client, [
				'name' => 'attach-module',
				'group' => 'meta',
				'description' => 'Attaches a module to any timer or command',
				'guildOnly' => true,
				'args' => [
					[
						'key' => 'module',
						'prompt' => 'Name of your module you want to attach to',
						'type' => 'string',
						'default' => ''
					]
				]
			]);
		}

		function run(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$module = $args['module'];
			$attachableTo = \Animeshz\ClusterPlus\Models\Module::ATTACHABLE_TO;

			$attachments = '';
			for ($i = 0; $i<\count($attachableTo); $i++) {
				$attachments .= ($i+1).' '.$attachableTo[$i].\PHP_EOL;
			}
			$message->say('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['description' => 'Where do you want to attach this module'.\PHP_EOL.$attachments])]);


			$selected = [];
			$q = 0;
			$listener = function (\CharlotteDunois\Yasmin\Models\Message $msg) use ($attachableTo, $message, &$selected, &$q, &$listener)
			{
				if($msg->channel === $message->message->channel && $msg->author === $message->message->author) {
					if ($q === 0) {
						$select = (int) $msg->content;
						$count = \count($attachableTo);

						if (!($select <= $count)) return $message->say('Wrong Option choose between 1 and '.$count);
						$selected['option'] = $attachableTo[($select-1)];
						$prompt = \constant('\Animeshz\ClusterPlusModels\Module::'.\strtoupper($selected['option']));

						if ($selected['option'] === 'command') {
							//no commands found issue
							$options = $GLOBALS['collector']->commands;
							var_dump($options);
							if(isset($options)) {
								$option = $options->first(function ($commandCollection, $guild)
								{
									if($guild === $msg->guild->id) return true;
								});
								if ($option !== null) {
									$prompt .= ' Available commands are: %s'.\PHP_EOL;
									$option->each(function ($cmd, $name) use ($prompt)
									{
										$prompt .= $name.\PHP_EOL;
									});
								} else {
									$prompt .= ' Commands not found, please create a command';
								}
							}
						}


						$message->say('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['description' => $prompt])]);
						++$q;
					} elseif ($q === 1 && !empty($selected)) {
						//prompt for validation

					} else {
						return $message->say('Wrong Option try again');
					}
				}
			};
			$this->client->on('message', $listener);
		}
	});
};