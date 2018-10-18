<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

require __DIR__.'/vendor/autoload.php';
$config = json_decode(file_get_contents(__DIR__.'/config.json'), true);

function inspectClosures($val, $prev = '') {
	if($prev === '' && is_subclass_of($val, \CharlotteDunois\Yasmin\Models\Base::class)) {
		file_put_contents('php://stdout', 'Checking '.get_class($val).PHP_EOL, FILE_APPEND);
	}
	
	if($val instanceof \Closure) {
		file_put_contents('php://stdout', 'Closure: '.$prev.PHP_EOL, FILE_APPEND);
	}
	
	if(!is_array($val) && !is_object($val)) {
		return;
	}
	
	foreach($val as $key => $v) {
		inspectClosures($v, $prev.'['.$key.']');
	}
}

if(class_exists("\\Animeshz\\ClusterPlus\\Client")) {
	$client = new \Animeshz\ClusterPlus\Client($config['clientConfig']);
	$client->login($config['token']);
	$client->loop->run();

	
	// var_dump(\serialize($client));
}