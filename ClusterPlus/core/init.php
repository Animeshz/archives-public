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
	 * @var \ClusterPlus\commands\CommandsRegistry
	 */
	// protected $commandsRegistry;

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

		// $this->attachListeners()->loadCore()->login();
		$this->loadCore()->login();
	}

	public function registerDefaults()
	{

		new \ClusterPlus\defaults\EventHandler($this->client);
	}

	public function attachListeners(string $location)
	{
		$path = \realpath($location);
		if($path){
			$listener = include $path;
			$instance = $listener($this->client);
			if($instance instanceof \ClusterPlus\interfaces\EventHandler){
				new $instance();
			} else {
				throw new \Exception("Wrong Argument: Event Handler must implement \ClusterPlus\interface\EventHandler", code, previous);
			}
		}
		return $this;
	}

	public function loadCore()
	{
		$factory = new \React\MySQL\Factory($this->loop);
		$factory->createConnection($this->config['database']['user'].':'.$this->config['database']['pass'].'@'.$this->config['database']['server'].'/'.$this->config['database']['db'])->done(function (\React\MySQL\ConnectionInterface $db)
		{
			$this->client->setProvider(new \CharlotteDunois\Livia\Providers\MySQLProvider($db));
		});

		return $this;
	}

	public function loadClasses()
	{
		return \HaydenPierce\ClassFinder\ClassFinder::getClassesInNamespace("ClusterPlus");
	}

	public function login(callable $resolve, callable $reject)
	{
		$this->client->login($this->config['token']);
		$this->loop->run();
	}
}