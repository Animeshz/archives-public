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
use CharlotteDunois\Yasmin\Models\Message;

return function(Client $client) {
	return (new class($client) extends SarahCommand {
		function __construct(Client $client) {
			parent::__construct($client, [
				'name' => 'form-create',
				'group' => 'forms',
				'description' => 'Creates a form, apply command canbe used to fill it',
				'guildOnly' => true,
				'userPermissions' => [
					'MANAGE_GUILD',
					'MANAGE_CHANNELS'
				]
			]);
		}

		function threadRun(CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$message->say('', [
				'embed' => new MessageEmbed([
					'description' => 'Welcome to question setup, type "done" when you have finished inputting your questions or "cancel" to cancel the setup.',
					'color' => '3447003'
				])
			]);

			$q = 1;
			$questions = [];
			$message->say('', [
				'embed' => new MessageEmbed([
					'description' => 'Give your form a title',
					'color' => '3447003'
				])
			]);

			$listener = function(Message $msg) use ($message, &$q, &$questions, &$listener)
			{
				if ($msg->author === $message->message->author && $msg->channel === $message->message->channel) {
					if ($msg->content === 'done') {
						$title = array_shift($questions);
						$forms = $message->client->provider->get($message->guild, 'forms');
						if( $forms === null ) {
							$forms = [$title => $questions];
						} else {
							$forms[$title] = $questions;
						}
						$message->client->provider->set($message->guild, 'forms', $forms)->then(function () use ($message)
						{
							return $message->say('', [
								'embed' => new MessageEmbed([
									'description' => 'Form created successfully',
									'color' => '3447003'
								])
							]);
						});
						return $message->client->removeListener('message', $listener);
					} elseif ($msg->content === 'cancel') {
						unset($q, $questions);
						$message->say('', [
							'embed' => new MessageEmbed([
								'description' => 'Cancelled form creation',
								'color' => '3447003'
							])
						]);
						return $message->client->removeListener('message', $listener);
					} else {
						$questions[] = $msg->content;
						return $message->say('', [
							'embed' => new MessageEmbed( [
								'description' => 'Write your question:' . $q,
								'color' => '3447003'
							])
						])
						->done(function () use (&$q) {
							++$q;
						});
					}
				}
			};

			$message->client->on('message', $listener);
		}
	});
};