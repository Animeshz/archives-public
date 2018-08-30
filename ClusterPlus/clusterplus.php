<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

class ClusterPlus
{
	public function __construct()
	{
		if(\PHP_SAPI !== 'cli') {
			throw new \Exception('ClusterPlus can only be used in the CLI SAPI. Please use PHP CLI.');
		}

		require __DIR__.'/vendor/autoload.php';
		$config = json_decode(file_get_contents(__DIR__.'/config.json'), true);

		if(class_exists("\\ClusterPlus\\init")) new \ClusterPlus\init($config);
	}
}

new ClusterPlus();