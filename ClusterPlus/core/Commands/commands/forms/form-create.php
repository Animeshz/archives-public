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
				'name' => 'form-create',
				'group' => 'clusterplus_forms',
				'description' => 'Creates a form, apply command canbe used to fill it',
				'guildOnly' => true,
			));
		}

		function run(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$message->channel->send('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003', 'description' => 'Enter title for the form you wanna create, to cancel command send cancel'])]);

			$questions = [];
			$listener = function (\CharlotteDunois\Yasmin\Models\Message $msg) use ($message, &$listener, &$questions) {
				if($msg->channel->__toString() === $message->channel->__toString() && $msg->author->__toString() === $message->author->__toString()) {

					if($msg->content === 'cancel') {

						unset($questions);
						$this->client->removeListener('message', $listener);
						$msg->channel->send('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003', 'description' => 'Successfully cancelled form creation'])]);

					} elseif($msg->content === 'done') {

						$title = \array_shift($questions);
						$data = $this->client->provider->get($msg->guild, 'forms', []);
						$data[$title] = $questions;

						$this->client->provider->set($msg->guild, 'forms', $data);
						$this->client->removeListener('message', $listener);
						//new listenener and send confirmation
						$msg->channel->send('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003', 'description' => 'Successfully created form'])]);

					} else {

						$questions[] = $msg->content;
						$msg->channel->send('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003', 'description' => 'Next question'])]);

					}

				}
			};
			$this->client->on('message', $listener);
		}
	});
};