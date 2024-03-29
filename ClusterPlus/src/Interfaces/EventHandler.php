<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Interfaces;

/**
 * Attaches listener to the client
 *
 * @property \CharlotteDunois\Livia\Client<\Animeshz\Client>   $client   Instance of current client.
 */
interface EventHandler
{
	/**
	 * Registers all the events that are being registered.
	 */
	function dispatch();
}