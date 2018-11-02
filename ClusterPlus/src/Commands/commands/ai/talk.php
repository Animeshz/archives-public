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
				'name' => 'talk',
				'group' => 'ai',
				'description' => 'Talk to me!',
				'args' => [
					[
						'key' => 'request',
						'prompt' => 'Ask me anything, I will try my best to answer you.',
						'type' => 'string'
					]
				]
			]);
		}

		function threadRun(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$answer = $this->client->dialogflow->getAnswer($args['request'], $message->author->id);
			$message->message->channel->send();
		}
	});
};