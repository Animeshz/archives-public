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
use React\Promise\ExtendedPromiseInterface;

return function (Client $client) {
    return (new class($client) extends Command {
        function __construct(Client $client) {
            parent::__construct($client, [
                'name' => 'invite',
                'group' => 'meta',
                'description' => 'Makes url to invite bot to any server',
                'details' => 'Invite is made of OAuth2 api',
                'examples' => ['invite'],
                'guarded' => true
            ]);
        }
        
        function threadRun(CommandMessage $message, \ArrayObject $args, bool $fromPattern) {
        	return $message->client->fetchApplication()->done(
        		function($oauth2) use ($message): ExtendedPromiseInterface
        		{
        			return $message->say('Invite me on your server by https://discordapp.com/oauth2/authorize?client_id='.$oauth2->id.'&permissions=8&scope=bot');
        		}
        	);
        }
    });
};