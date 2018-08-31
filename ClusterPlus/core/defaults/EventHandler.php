<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace ClusterPlus;

/**
 * Attaches listener to the client
 *
 * @property \CharlotteDunois\Livia\Client<\CharlotteDunois\Yasmin\Client>   $client   Instance of current client.
 */
class EventHandler implements \ClusterPlus\interfaces\EventHandler
{
	/**
	 * @var \CharlotteDunois\Livia\LiviaClient<\CharlotteDunois\Yasmin\Client>
	 */
	protected $client;

	public function __construct(\CharlotteDunois\Yasmin\Client $client)
	{
		$this->client = $client;

		foreach (get_class_methods($this) as $event) {
			if($event === '__construct'){ continue; }
			$this->$event();
		}
	}

	protected function ready()
	{
		$this->client->on('ready', function ()
		{
			echo 'Logged in as '.$this->client->user->tag.' created on '.$this->client->user->createdAt->format('d.m.Y H:i:s').PHP_EOL;
		});
	}

	// protected function 
}