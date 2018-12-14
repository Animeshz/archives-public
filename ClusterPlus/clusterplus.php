<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

require __DIR__.'/vendor/autoload.php';
$config = json_decode(file_get_contents(__DIR__.'/config.json'), true);

$client = new \Animeshz\ClusterPlus\Client($config['clientConfig']);
// $stream = new \React\Stream\ReadableResourceStream(STDIN, $client->loop);

// $stream->on('data', function (string $data) use ($client) {
// 	if($data === 'stop') {
// 		$client->destroy();
// 		echo 'destroy';
// 	}
// });

$client->login($config['token']);
$client->loop->run();
