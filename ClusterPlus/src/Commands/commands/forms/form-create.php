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

						$this->client->removeListener('message', $listener);
						$newListener = function (\CharlotteDunois\Yasmin\Models\Message $msg) use ($message, &$questions, &$newListener) {
							if($msg->channel->__toString() === $message->channel->__toString() && $msg->author->__toString() === $message->author->__toString()) {
								if($msg->content === 'yes') {
									$desc = 'Title: '.$questions[0].\PHP_EOL;
									for($i = 1; $i<\count($questions); $i++) {
										$desc .= $i.'. '.$questions[$i];
									}

									$embed = new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003']);
									$embed->setDescription($desc);

									$msg->channel->send('', ['embed' => $embed]);
								} else {
									unset($questions);
									$this->client->removeListener('message', $newListener);
									$msg->channel->send('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003', 'description' => 'Successfully cancelled form creation'])]);
								}
							}
						};
						$this->client->on('message', $listener);
					} elseif($msg->content === 'yes') {
						$data = $this->client->provider->get($msg->guild, 'forms', []);
						$data[$title] = $questions;

						$this->client->provider->set($msg->guild, 'forms', $data);
						$this->client->removeListener('message', $listener);
						$msg->channel->send('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003', 'description' => 'Successfully created form'])]);
					} else {

						$questions[] = $msg->content;
						$msg->channel->send('', ['embed' => new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003', 'description' => 'Enter a new question, type done for submitting and cancel for cancelling'])]);

					}

				}
			};
			$this->client->on('message', $listener);
		}
	});
};