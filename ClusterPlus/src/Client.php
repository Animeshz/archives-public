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
class Client
{
	/**
	 * @var \CharlotteDunois\Livia\LiviaClient<\CharlotteDunois\Yasmin\Client>
	 */
	protected $client;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * Fancy Constructor
	 *
	 * ```
	 * [
	 *   'clientConfig' => [], (you client config i've included see mapping of phpdoc)
	 *   'internal.dependent.eventHandler.instance' => '', (classname of eventhandler and it must extends ClusterPlus\Dependent\EventHandler) (optional)
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
	 * @param array								$config		Array of config.
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
		$this->client = new \CharlotteDunois\Livia\LiviaClient($this->config['clientConfig'], $this->loop);

		$this->validateConfigs($config);
		$this->setMissings();

		$this->eventHandler = new $this->config['internal.dependent.eventHandler.instance']($this);
		$this->loadCore();
	}

	public function __get(string $name)
	{
		switch ($name) {
			case 'client':
			return $this->client;
			break;
		}
		if(property_exists($this, $name)) return $this->name;

		throw new \RuntimeException('Unknown property '.\get_class($this).'::$'.$name);
	}

	/**
	 * Sets the passed config dependencies if not exists.
	 * @return void
	 * @throws \RuntimeException
	 */
	protected function setMissings()
	{
		$storages = array(
			'eventHandler.instance' => '\\ClusterPlus\\Dependent\\EventHandler'
		);

		foreach($storages as $name => $base) {
			if(empty($this->config['internal.dependent.'.$name])) {
				$this->config['internal.dependent.'.$name] = $base;
			}
		}
	}

	public function loadCore()
	{
		$this->eventHandler->dispatch();
		$GLOBALS['collector'] = new \ClusterPlus\Utils\Collector($this->client);
		$factory = new \React\MySQL\Factory($this->loop);
		$factory->createConnection($this->config['database']['user'].':'.$this->config['database']['pass'].'@'.$this->config['database']['server'].'/'.$this->config['database']['db'])->done(function (\React\MySQL\ConnectionInterface $db)
		{
			$this->client->setProvider(new \CharlotteDunois\Livia\Providers\MySQLProvider($db))->done(function ()
			{
				global $collector;
				$collector->loadFromDB();
			});
		});
		new \ClusterPlus\Commands\CommandsDispatcher($this->client); //refractor to dispatcher
	}

	// public function loadClasses()
	// {
	// 	return \HaydenPierce\ClassFinder\ClassFinder::getClassesInNamespace("ClusterPlus");
	// }

	public function login(callable $resolve = null, callable $reject = null)
	{
		$this->client->login($this->config['token'])->done($resolve, $reject);
		$this->loop->run();
	}

	/**
	 * Validates the passed config.
	 * @param array  $config
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function validateConfigs(array $config) {
		$validator = \CharlotteDunois\Validation\Validator::make($config, array(
			'internal.dependent.eventHandler.instance' => 'class:ClusterPlus\\Dependent\\EventHandler,string_only'
		));

		if($validator->fails()) {
			$errors = $validator->errors();

			$name = \array_keys($errors)[0];
			$error = $errors[$name];

			throw new \InvalidArgumentException('Client Option '.$name.' '.\lcfirst($error));
		}
	}
}