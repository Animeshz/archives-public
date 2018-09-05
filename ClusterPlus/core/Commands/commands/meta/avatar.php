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
				'name' => 'avatar',
				'group' => 'clusterplus_meta',
				'description' => 'Shows the avatar of the user if not found nothing is sent',
				'guildOnly' => true,
				'args' => array(
					array(
						'key' => 'user',
						'prompt' => 'user',
						'type' => 'user',
						'default' => ''
					)
				)
			));
		}

		function run(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$embed = new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003']);
			if($args['user'] !== '') {
				$av = $args['user']->getAvatarURL();
				if($av !== null) {
					$embed->setImage($av);
				} else {
					$embed->setDescription('Image not found');
				}
				$message->channel->send('', ['embed' => $embed]);
			} else {
				$embed->setDescription('mention a user or send name or tag');
				$message->channel->send('', ['embed' => $embed]);

				$listener = function (\CharlotteDunois\Yasmin\Models\Message $msg) use ($message, &$listener) {
					if($msg->channel->__toString() === $message->channel->__toString() && $msg->author->__toString() === $message->author->__toString()) {

						$embed = new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003']);
						$removeListener = true;
						if($msg->mentions->members->first() !== null) {
							$av = $msg->mentions->members->first()->user->getAvatarURL();
							if($av !== null) {
								$embed->setImage($av);
							} else {
								$embed->setDescription('User found, Image not found');
							}
						} else {
							$found = $this->client->users->filter(function($user) use ($msg) { return (mb_stripos(mb_strtolower($user->tag), mb_strtolower($msg->content))); });
							$count = $found->count();

							if($count === 0) $embed->setDescription('User not found.');

							if($count === 1) {
								$av = $found->first()->getAvatarURL();
								if($av !== null) {
									$embed->setImage($av);
								} else {
									$embed->setDescription('User found, Image not found');
								}
							}

							if($count > 1) {
								$desc = \CharlotteDunois\Livia\Utils\DataHelpers::disambiguation($found, 'users', null).\PHP_EOL;
								$embed->setDescription($desc);
								$removeListener = false;
							}
						}
						return $msg->channel->send('', ['embed' => $embed])->done(function() use ($removeListener, &$listener) { ($removeListener === false) ?: $this->client->removeListener('message', $listener); });
					}
				};
				$this->client->on('message', $listener);
			}
		}
	});
};