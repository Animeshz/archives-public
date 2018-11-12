<?php
/**
 * Converts phrase to a fancy unicoded phrase
 * 
 * @package    ClusterPlus
 * @author     Animesh Sahu <animeshsahu19@yahoo.com>
 */

use Animeshz\ClusterPlus\Client;
use Animeshz\ClusterPlus\Dependent\Command;
use Animeshz\ClusterPlus\Utils\CommandHelpers;
use \CharlotteDunois\Livia\CommandMessage;

return function ($client) {
	return (new class($client) extends Command {
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
		
		function run(CommandMessage $message, \ArrayObject $args, bool $fromPattern) {
			return $message->reply(CommandHelpers::unicodeConvert($args['phrase'], $args['type']));
		}
	});
};