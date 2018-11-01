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
				'name' => 'avatar',
				'group' => 'meta',
				'description' => 'Shows the avatar of the user if not found nothing is sent',
				'details' => 'Avatar is profile picture of a user',
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

		function threadRun(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
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