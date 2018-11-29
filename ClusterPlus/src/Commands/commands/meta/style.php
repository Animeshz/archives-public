<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

use Animeshz\ClusterPlus\Client;
use Animeshz\ClusterPlus\Utils\CommandHelpers;
use CharlotteDunois\Sarah\SarahCommand;
use \CharlotteDunois\Livia\CommandMessage;

return function ($client) {
	return (new class($client) extends SarahCommand {
		function __construct(Client $client) {
			parent::__construct($client, [
				'name' => 'style',
				'group' => 'meta',
				'description' => 'Converts phrase to a fancy unicoded phrase',
				'examples' => ['style Hello World!'],
				'args' => [
					[
						'key' => 'phrase',
						'prompt' => 'Phrase you want to fancy',
						'type' => 'string'
					],
					[
						'key' => 'type',
						'prompt' => 'Send the number of identity, List of usable identities are:'.\PHP_EOL.CommandHelpers::getUnicodeSamples(),
						'type' => 'integer',
						'min' => 0,
						'max' => CommandHelpers::getUnicodeTypesCount() - 1
					]
				],
				'guarded' => true
			]);
		}
		
		function threadRun(CommandMessage $message, \ArrayObject $args, bool $fromPattern) {
			return $message->reply(CommandHelpers::unicodeConvert($args['phrase'], $args['type']));
		}
	});
};