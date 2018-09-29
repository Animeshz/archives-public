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
				'name' => 'avatar',
				'group' => 'meta',
				'description' => 'Shows the avatar of the user if not found nothing is sent',
				'guildOnly' => true,
				'args' => [
					[
						'key' => 'user',
						'prompt' => 'user',
						'type' => 'user',
						'default' => ''
					]
				]
			]);
		}

		function run(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$embed = new \CharlotteDunois\Yasmin\Models\MessageEmbed(['color'=> '3447003']);
			if($args['user'] !== '') {
				$av = $args['user']->getAvatarURL();
			} else {
				$av = $message->message->author->getAvatarURL();
			}

			if($av !== null) {
				$embed->setImage($av);
			} else {
				$embed->setDescription('Image not found');
			}

			$message->message->channel->send('', ['embed' => $embed]);
		}
	});
};