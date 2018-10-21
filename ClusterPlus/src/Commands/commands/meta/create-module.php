<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

return function(\Animeshz\ClusterPlus\Client $client) {
	return (new class($client) extends \Animeshz\ClusterPlus\Dependent\Command {
		function __construct($client) {
			parent::__construct($client, [
				'name' => 'create-module',
				'group' => 'meta',
				'description' => 'Creates a module',
				'guildOnly' => true,
				'args' => [
					// [
					// 	'key' => 'actions',
					// 	'prompt' => 'Define some actions',
					// 	'type' => 'string',
					// 	'infinite' => true
					// ]
				]
			]);
		}

		function threadRun(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			//get data
			$description = 'What thing you waanna assign to the command? Available options are:'.PHP_EOL;
			for ($i=0; $i<\count(\Animeshz\ClusterPlus\Utils\CommandHelpers::$options); $i++) { 
				$description .= ($i+1).'. '.\Animeshz\ClusterPlus\Utils\CommandHelpers::$options[$i].PHP_EOL;
			}
			$message->channel->send('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003', 'description' => $description])]);

			$selected = [];
			$listener = function (\CharlotteDunois\Yasmin\Models\Message $msg) use ($selected, $message, &$listener) {
				if($msg->channel === $message->channel && $msg->author === $message->author) {

					$m = (int)$msg->content;
					if($m<=\count(\Animeshz\ClusterPlusUtils\CommandHelpers::$options)) {
						$option = \Animeshz\ClusterPlusUtils\CommandHelpers::$options[($m-1)];
						$selected['option'] = $option;
						$description = 'Which thing you waanna assign to the '.$option.'? Available methods are: '.PHP_EOL;
						for ($i=0; $i<\count(\Animeshz\ClusterPlusUtils\CommandHelpers::$methods[$option]); $i++) { 
							$description .= ($i+1).'. '.\Animeshz\ClusterPlusUtils\CommandHelpers::$methods[$option][$i].PHP_EOL;
						}
					}
					return $msg->channel->send('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003', 'description' => $description])])->done(function () use (&$listener)
					{
						$this->client->removeListener('message', $listener);
					});
				}
			};
			$this->client->on('message', $listener);

			// validate data
			//register command in database
			//create another class for maintaining the command created
		}
	});
};