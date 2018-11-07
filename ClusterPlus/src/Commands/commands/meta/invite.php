<?php
/**
 * sends link to invite the bot to any server
 * 
 * @package    ClusterPlus
 * @author     Animesh Sahu <animeshsahu19@yahoo.com>
 */

return function ($client) {
    return (new class($client) extends \Animeshz\ClusterPlus\Dependent\Command {
        function __construct(\CharlotteDunois\Livia\LiviaClient $client) {
            parent::__construct($client, [
                'name' => 'invite',
                'group' => 'meta',
                'description' => 'Makes url to invite bot to any server',
                'details' => 'Invite is made of OAuth2 api',
                'examples' => ['invite'],
                'guarded' => true
            ]);
        }
        
        function threadRun(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern) {
        	return $message->client->fetchApplication()->done(
        		function( $oauth2 ) use ( $message )
        		{
        			return $message->say( 'Invite me on your server by https://discordapp.com/oauth2/authorize?client_id=' . $oauth2->id . '&permissions=8&scope=bot' );
        		}
        	);
        }
    });
};