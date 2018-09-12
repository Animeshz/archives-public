<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

class ClusterPlus
{
	/**
	 * @var \ClusterPlus\Client
	 */
	protected $client;

	public function __construct()
	{
		require __DIR__.'/../vendor/autoload.php';
		$config = json_decode(file_get_contents(__DIR__.'/../config.json'), true);

		if(class_exists("\\ClusterPlus\\Client")) $this->client = new \ClusterPlus\Client($config);
	}

	public function __get($name){
		switch ($name) {
			case 'client':
			return $this->client;
			break;
		}
	}
}

$cp = new ClusterPlus();
$cp->client->login();