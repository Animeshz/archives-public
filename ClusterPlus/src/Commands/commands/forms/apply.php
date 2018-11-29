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

return function (Client $client) {
	return (new class($client) extends SarahCommand {
		function __construct(Client $client) {
			parent::__construct($client, [
				'name' => 'apply',
				'alias' => [ 'apply-form' ],
				'group' => 'forms',
				'description' => 'Shows forms',
				'details' => 'Form stores an array of data filled by a user. This command must be run in a guild/server',
				'examples' => [ 'apply', 'apply-forms' ],
				'guildOnly' => true,
				'guarded' => true
			]);
		}
		
		function run(CommandMessage $message, \ArrayObject $args, bool $fromPattern) {
			$forms = $message->client->provider->get($message->guild, 'forms');
			if( $forms !== null ) {
				$q = 1;
				foreach ($forms as $title => $questions) {
					$titles[] = $q.'. '.$title;
					$q++;
				}
				$title = \implode(PHP_EOL, $titles);
				$message->say( '', [
					'embed' => new MessageEmbed( [
						'description' => 'Form you want to fill, reply with number of form.'.PHP_EOL.$title,
						'color' => '3447003'
					])
				]);

				$q = 0;
				$title = '';
				$questions = [];
				$answers = [];
				$listener = function(Message $msg) use ($message, &$title, $titles, $forms, &$q, &$questions, &$answers, &$listener)
				{
					if ($msg->author === $message->message->author && $msg->channel === $message->message->channel) {
						if($q === 0) {
							$formNo = (int) trim($msg->content);
							if(!empty( $formNo)){
								if(isset($titles[($formNo-1)])) {
									$title = $titles[($formNo-1)];
									$title = str_replace("$formNo. ", '', $title);
									$questions = $forms[$title];
									$message->say('', [
										'embed' => new MessageEmbed([
											'description' => 'TIP: For cancelling the form, send "cancel".' . PHP_EOL . $questions[0],
											'color' => '3447003'
										])
									]);
									++$q;
								} else {
									$message->say('', [
										'embed' => new MessageEmbed( [
											'description' => 'Wrong argument, Cancelled command',
											'color' => '3447003'
										])
									]);
									return $message->client->removeListener('message', $listener);
								}
							} else {
								$message->say('', [
									'embed' => new MessageEmbed( [
										'description' => 'Wrong argument, Cancelled command',
										'color' => '3447003'
									])
								]);
								return $message->client->removeListener('message', $listener);
							}
						} else {
							if( $q === count($questions) ) {
								$answers[] = $msg->content;
								$embed = new MessageEmbed([
									'color' => '3447003',
									'description' => 'Are these information correct? say yes for submit or no for cancel. (If you previously submitted your form, this data will be updated)'
								]);
								for( $i=0; $i<count($questions); $i++ ) {
									$embed->addField($questions[$i], $answers[$i]);
								}
								$message->say( '', [
									'embed' => $embed
								])
								->done( function() use ( &$q ) {
									++$q;
								});
							} elseif ( $q === (count($questions)+1) ) {
								if( $msg->content === 'yes' ) {
									$FormData = $message->client->provider->getFormData($message->guild, $message->author->id, []);
									$FormData[$title] = $answers;

									$message->client->provider->setFormData($message->guild, $message->author->id, $FormData)->done(function() use ($message)
									{
										$message->say( '', [
											'embed' => new MessageEmbed( [
												'description' => 'Successfully applied to form',
												'color' => '3447003'
											])
										]);
									});
									return $message->client->removeListener('message', $listener);
								} else {
									$message->say( '', [
										'embed' => new MessageEmbed( [
											'description' => 'Cancelled form',
											'color' => '3447003'
										])
									]);
									return $message->client->removeListener('message', $listener);
								}
							} else {
								if( $msg->content === 'cancel' ) {
									unset( $q, $questions, $answers );
									$message->say( '', [
										'embed' => new MessageEmbed( [
											'description' => 'Cancelled form',
											'color' => '3447003'
										])
									]);
									return $message->client->removeListener('message', $listener);
								} else {
									$answers[] = $msg->content;
									return $message->say( '', [
										'embed' => new MessageEmbed( [
											'description' => 'TIP: For cancelling the form, send "cancel".' . PHP_EOL . $questions[($q)],
											'color' => '3447003'
										])
									])->done(function () use (&$q) { ++$q; });
								}
							}
						}
					}
				};

				$message->client->on('message', $listener);
			}
		}
	});
};