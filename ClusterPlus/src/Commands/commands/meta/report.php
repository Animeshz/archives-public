<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

use Animeshz\ClusterPlus\Client;
use Animeshz\ClusterPlus\Dependent\Command;
use Animeshz\ClusterPlus\Utils\CommandHelpers;
use CharlotteDunois\Livia\CommandMessage;
use CharlotteDunois\Yasmin\Models\MessageEmbed;

return function (Client $client) {
	return (new class($client) extends Command {
		function __construct(Client $client) {
			parent::__construct($client, [
				'name' => 'report',
				'aliases' => ['bug', 'bugreport'],
				'group' => 'meta',
				'description' => 'Send any bug/issue/report to main server for the fix',
				'details' => 'Report an issue',
				'examples' => ['report', 'bug', 'bugreport'],
				'guildOnly' => true,
				'args' => [
					[
						'key' => 'report',
						'prompt' => 'Send info about the bugs/report, you can use imgur to send pics or vimeo for video',
						'type' => 'string'
					]
				],
				'guarded' => true
			]);
		}
		
		function threadRun(CommandMessage $message, \ArrayObject $args, bool $fromPattern) {
			$toSend = \filter_var(\trim( $args['report']));
			if ($toSend === '' || $toSend === null) {
				return;
			}

			$user = $message->message->author;
			$options = $this->client->getOption('guild.info');

			$guild = $this->client->guilds->first(function($value, $key) use ($options)
			{
				if ($key ===  $options['guild.id']) {
					return true;
				}
			});

			if ($guild !== null) {
				$channel = $guild->channels->first(function($value, $key) use ($options)
				{
					if ($key === $options['channel.report.id']) {
						return true;
					}
				});
				unset($guild);
			} else {
				$message->reply('An error occurred, we\'ll fix it soon. Sorry but report didn\'t submitted.');
			}

			$embed = new MessageEmbed(['color' => '3447003']);
			$embed->setTitle($user->tag)
			->addField('Message', $toSend)
			->addField('META', 'GUILD: '.$message->guild->name.'<'.$message->guild->id.'>'.PHP_EOL.'CHANNEL: '.$message->channel->name.'<'.$message->channel->id.'>')
			->setFooter('ID: ' . $user->id)
			->setTimestamp();

			return $channel->send('',
				[ 'embed' => $embed ]
			)->done( function() use ( $message )
			{
				$message->reply('Report successfully submitted.');
			});
		}
	});
};