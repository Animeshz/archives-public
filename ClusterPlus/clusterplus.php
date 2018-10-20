<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

require __DIR__.'/vendor/autoload.php';
$config = json_decode(file_get_contents(__DIR__.'/config.json'), true);

if(class_exists("\\Animeshz\\ClusterPlus\\Client")) {
	$client = new \Animeshz\ClusterPlus\Client($config['clientConfig']);
	$client->login($config['token']);
	$client->loop->run();
}