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
	 * @var \ClusterPlus\init
	 */
	protected $init;

	public function __construct()
	{
		require __DIR__.'/vendor/autoload.php';
		$config = json_decode(file_get_contents(__DIR__.'/config.json'), true);

		if(class_exists("\\ClusterPlus\\init")) $this->init = new \ClusterPlus\init($config);
	}

	public function __get($name){
		switch ($name) {
			case 'init':
				return $this->init;
			break;
		}
	}

	public function getInitInstance()
	{
		return $this->init;
	}
}

$cp = new ClusterPlus();

$cp->init->registerDefaults();