<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus;

/**
 * ClusterPlus Client
 */
class Client extends \CharlotteDunois\Livia\LiviaClient
{
	/**
	 * @var \Animeshz\ClusterPlus\Utils\Collector
	 */
	protected $collector;

	/**
	 * Fancy Constructor
	 *
	 * ```
	 * [
	 *   'internal.dependent.eventHandler.instance' => '', (classname of eventhandler and it must extends Animeshz\ClusterPlus\Dependent\EventHandler) (optional)
	 *   'database' => [
	 *      "server": "",
	 *      "user": "",
	 *      "pass": "",
	 *      "db": ""
	 *   ]
	 * ]
	 * ```
	 *
	 * @param array								$config		Any client options and options listed below.
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 * 
	 * @see https://livia.neko.run/master/CharlotteDunois/Livia/LiviaClient.html#method___construct
	 * @see https://yasmin.neko.run/master/CharlotteDunois/Yasmin/Client.html#method___construct
	 */
	public function __construct(array $config = [], ?\React\EventLoop\LoopInterface $loop = null)
	{
		$this->validateConfigs($config);
		parent::__construct($config, $loop);

		$eventHandler = $this->getOption('internal.dependent.eventHandler.instance', '\\Animeshz\\ClusterPlus\\Dependent\\EventHandler');
		$this->eventHandler = new $eventHandler($this);
		$this->eventHandler->dispatch();

		$this->collector = new \Animeshz\ClusterPlus\Utils\Collector($this);
		$factory = new \React\MySQL\Factory($this->loop);
		$factory->createConnection($this->getOption('database')['user'].':'.$this->getOption('database')['pass'].'@'.$this->getOption('database')['server'].'/'.$this->getOption('database')['db'])->done(function (\React\MySQL\ConnectionInterface $db)
		{
			$this->setProvider(new \CharlotteDunois\Livia\Providers\MySQLProvider($db))->done(function ()
			{
				$this->collector->loadFromDB();
			});
		});
		new \Animeshz\ClusterPlus\Commands\CommandsDispatcher($this);

	// serializate modules in command.
	}

	/**
     * @param string  $name
     * @return mixed
     * @throws \Exception
     * @internal
     */
	function __get($name)
	{
		switch($name) {
			case 'collector':
			return $this->collector;
			break;
		}

		return parent::__get($name);
	}

	/**
	 * Validates the passed config.
	 * @param array  $config
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function validateConfigs(array $config): void
	{
		$validator = \CharlotteDunois\Validation\Validator::make($config, array(
			'internal.dependent.eventHandler.instance' => 'class:\\Animeshz\\ClusterPlus\\Dependent\\EventHandler,string_only'
		));

		if($validator->fails()) {
			$errors = $validator->errors();

			$name = \array_keys($errors)[0];
			$error = $errors[$name];

			throw new \InvalidArgumentException('Client Option '.$name.' '.\lcfirst($error));
		}
	}
}