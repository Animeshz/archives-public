<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace ClusterPlus;

/**
 * Initiate the application
 *
 * @property \CharlotteDunois\Livia\Client<\CharlotteDunois\Yasmin\Client>   $client   Instance of current client.
 */
class init
{
	/**
	 * @var \CharlotteDunois\Livia\LiviaClient<\CharlotteDunois\Yasmin\Client>
	 */
	protected $client;

	/**
     * Fancy Constructor
     *
     * ```
     * [
     *   
     * ]
     * ```
     *
     * @param array								$options		Array of client options.
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
	public function __construct(array $config)
	{
		$this->config = $config;
		$this->loop = \React\EventLoop\Factory::create();
		$this->client = new \CharlotteDunois\Livia\LiviaClient($config['clientConfig'], $this->loop);

		$this->attachListeners()->loadCore()->login();
	}

	public function attachListeners()
	{
		return $this;
	}

	public function loadCore()
	{
		$factory = new \React\MySQL\Factory($this->loop);
		$factory->createConnection($this->config['database']['user'].':'.$this->config['database']['pass'].'@'.$this->config['database']['server'].'/'.$this->config['database']['db'])->done(function (\React\MySQL\ConnectionInterface $db)
		{
			$this->client->setProvider(new \CharlotteDunois\Livia\Providers\MySQLProvider($db));
		});

		var_dump($this->loadClasses());

		return $this;
	}

	public function loadClasses()
	{
		return \HaydenPierce\ClassFinder\ClassFinder::getClassesInNamespace("ClusterPlus");
	}

	public function login()
	{
		$this->client->login($this->config['token']);
		$this->loop->run();
	}
}