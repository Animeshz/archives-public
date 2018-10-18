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
	 * @var \CharlotteDunois\Phoebe\Pool
	 */
	protected $pool;

	/**
	 * Fancy Constructor
	 *
	 * ```
	 * [
	 *   'eventHandler.class' => '', (classname of eventhandler and it must extends Animeshz\ClusterPlus\Dependent\EventHandler) (optional)
	 *   'pool.class' => '', (classname of pool and it must extends Animeshz\ClusterPlus\Dependent\Pool) (optional)
	 *   'worker.class' => '', (classname of worker and it must extends Animeshz\ClusterPlus\Dependent\Worker) (optional)
	 *   'database' => [
	 *      "server": "",
	 *      "user": "",
	 *      "pass": "",
	 *      "db": ""
	 *   ],
	 *   'pool.options' => [] (pool constructor options)
	 * ]
	 * ```
	 *
	 * @param array								$config		Any client options and options listed below.
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 * 
	 * @see https://livia.neko.run/master/CharlotteDunois/Livia/LiviaClient.html#method___construct
	 * @see https://yasmin.neko.run/master/CharlotteDunois/Yasmin/Client.html#method___construct
	 * @see https://charlottedunois.github.io/Phoebe/master/CharlotteDunois/Phoebe/Pool.html#method___construct
	 */
	public function __construct(array $config = [], ?\React\EventLoop\LoopInterface $loop = null)
	{
		$this->validateConfigs($config);
		parent::__construct($config, $loop);

		// if(\CharlotteDunois\Yasmin\Models\ClientBase::$serializeClient === null) {
		// 	\CharlotteDunois\Yasmin\Models\ClientBase::$serializeClient = $this;
		// }

		$eventHandler = $this->getOption('eventHandler.class', '\\Animeshz\\ClusterPlus\\Dependent\\EventHandler');
		$pool = $this->getOption('pool.class', '\\Animeshz\\ClusterPlus\\Dependent\\Pool');
		$worker = $this->getOption('worker.class', '\\Animeshz\\ClusterPlus\\Dependent\\Worker');
		$poolOptions = $this->getOption('pool.options', []);

		if(!isset($poolOptions['size'])) $poolOptions['size'] = 7;
		$poolOptions['worker'] = $worker;

		$this->eventHandler = new $eventHandler($this);
		$this->pool = new $pool($this, $poolOptions);

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

		$this->once('ready', function () {
			$this->pool->submitTask(new class extends \CharlotteDunois\Phoebe\AsyncTask{
				function run()
				{
					$this->wrap(null);
				}
			});
		});

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
     * @return string
     * @internal
     */
	function serialize()
	{
		$pool = $this->pool;
		$this->pool = null;

		$str = parent::serialize();
		$this->pool = $pool;

		return $str;
	}

	/**
     * @return \React\Promise\ExtendedPromiseInterface
     * @internal
     */
	function eval(string $code, array $options = array())
	{
		if(!(\Animeshz\ClusterPlus\Utils\UniversalHelpers::isValidPHP($code))) return;
		return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($code) {
			if(\mb_substr($code, -1) !== ';') {
				$code .= ';';
			}

			if(\mb_strpos($code, 'return') === false && \mb_strpos($code, 'echo') === false) {
				$code = \explode(';', $code);
				$code[(\count($code) - 2)] = \PHP_EOL.'return '.\trim($code[(\count($code) - 2)]);
				$code = \implode(';', $code);
			}

			$result = (function ($client, $code) {
				return eval($code);
			})($this, $code);

			if(!($result instanceof \React\Promise\PromiseInterface)) {
				return $resolve($result);
			}

			return $result;
		}));
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
			'eventHandler.class' => 'class:\\Animeshz\\ClusterPlus\\Dependent\\EventHandler,string_only',
			'pool.class' => 'class:\\Animeshz\\ClusterPlus\\Dependent\\Pool,string_only',
			'worker.class' => 'class:\\Animeshz\\ClusterPlus\\Dependent\\Worker,string_only'
		));

		if($validator->fails()) {
			$errors = $validator->errors();

			$name = \array_keys($errors)[0];
			$error = $errors[$name];

			throw new \InvalidArgumentException('Client Option '.$name.' '.\lcfirst($error));
		}
	}
}