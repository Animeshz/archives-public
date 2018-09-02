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
     *   'clientConfig' => [], (you client config i've included see mapping of phpdoc)
     *   'internal.eventHandler' => '', (classname of eventhandler and it must extends ClusterPlus\Dependent\EventHandler) (optional)
     *   'token' => '', (your client token)
     *   'database' => [
     *      "server": "",
	 *      "user": "",
	 *      "pass": "",
	 *      "db": ""
     *   ]
     * ]
     * ```
     *
     * @param array								$options		Array of client options.
     * @throws \Exception
     * @throws \InvalidArgumentException
     * 
     * @see https://livia.neko.run/master/CharlotteDunois/Livia/LiviaClient.html#method___construct
     * @see https://yasmin.neko.run/master/CharlotteDunois/Yasmin/Client.html#method___construct
     */
	public function __construct(array $config)
	{
		$this->config = $config;
		$this->loop = \React\EventLoop\Factory::create();
		$this->client = new \CharlotteDunois\Livia\LiviaClient($config['clientConfig'], $this->loop);

		$this->attachListeners()->loadCore()->login();
	}

	public function __get(string $name)
	{
		switch ($name) {
			case 'client':
			return $this->client;
			break;
			
			default:
			throw new \RuntimeException('Unknown property '.\get_class($this).'::$'.$name);
			break;
		}
	}

	protected function attachListeners()
	{
		if(isset($this->config['internal.eventHandler']) && is_subclass_of($this->config['internal.eventHandler'], '\ClusterPlus\dependent\EventHandler')) $this->eventHandler = new $this->config['internal.eventHandler'];
		if(!isset($this->eventHandler)) $this->eventHandler = new \ClusterPlus\defaults\EventHandler($this);

		return $this;
	}

	protected function loadCore()
	{
		$this->eventHandler->dispatch();
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

	protected function login(callable $resolve, callable $reject)
	{
		$this->client->login($this->config['token']);
		$this->loop->run();
	}
}