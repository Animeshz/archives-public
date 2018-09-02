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
                'group' => 'clusterplus',
                'description' => 'Shows the avatar of the user if not found nothing is sent',
                'guildOnly' => true,
                'args' => array(
                    array(
                        'key' => '',
                        'prompt' => '',
                        'type' => ''
                    )
                )
            ));
		}
	});
}