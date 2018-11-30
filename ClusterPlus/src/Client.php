<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
 */

namespace Animeshz\ClusterPlus;

use Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient;
use Animeshz\ClusterPlus\Commands\CommandsDispatcher;
use Animeshz\ClusterPlus\Models\Invite;
use Animeshz\ClusterPlus\Utils\Collector;
use Animeshz\ClusterPlus\Utils\UniversalHelpers;
use CharlotteDunois\Sarah\SarahClient;
use CharlotteDunois\Validation\Validator;
use CharlotteDunois\Yasmin\Models\ClientBase;
use InvalidArgumentException;
use React\EventLoop\LoopInterface;
use React\MySQL\Factory;
use React\MySQL\ConnectionInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use React\Promise\ExtendedPromiseInterface;

/**
 * ClusterPlus Client
 */
class Client extends SarahClient
{
	/**
	 * @var \Animeshz\ClusterPlus\Utils\Collector
	 */
	protected $collector;

	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient
	 */
	protected $dialogflow;

	/**
	 * Fancy Constructor
	 *
	 * ```
	 * [
	 *   'eventHandler.class' => '', (classname of eventhandler and it must extends Animeshz\ClusterPlus\Dependent\EventHandler) (optional)
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
	 * @see https://charlottedunois.github.io/Sarah/master/CharlotteDunois/Sarah/SarahClient.html#method___construct
	 * @see https://livia.neko.run/master/CharlotteDunois/Livia/LiviaClient.html#method___construct
	 * @see https://yasmin.neko.run/master/CharlotteDunois/Yasmin/Client.html#method___construct
	 * @see https://charlottedunois.github.io/Phoebe/master/CharlotteDunois/Phoebe/Pool.html#method___construct
	 */
	public function __construct(array $config = [], ?LoopInterface $loop = null)
	{
		$this->validateConfigs($config);
		parent::__construct($config, $loop);

		$eventHandler = $this->getOption('eventHandler.class', '\\Animeshz\\ClusterPlus\\Dependent\\EventHandler');
		$disabledEvents = $this->getOption('eventHandler.events.disable', []);
		$isDatabaseSet = $this->getOption('database', false);
		$isDialogflowFileSet = $this->getOption('dialogflow', false);

		if(!isset($poolOptions['size'])) $poolOptions['size'] = 7;
		$poolOptions['worker'] = $worker;

		$this->eventHandler = new $eventHandler($this, $disabledEvents);
		$this->eventHandler->dispatch();

		$this->collector = new Collector($this);

		if ($isDatabaseSet) {
			$factory = new Factory($this->loop);
			$factory->createConnection($this->getOption('database')['user'].':'.$this->getOption('database')['pass'].'@'.$this->getOption('database')['server'].'/'.$this->getOption('database')['db'])->then(function (ConnectionInterface $db)
			{
				$provider = $this->getOption('provider.class', '\\Animeshz\\ClusterPlus\\Dependent\\MySQLProvider');
				$this->setProvider(new $provider($db))->then(function (): ExtendedPromiseInterface
				{
					$this->emit('providerSet');
				});
			});
		}

		if ($isDialogflowFileSet) {
			$this->dialogflow = new DialogFlowClient($this);
			$this->dialogflow->on('error', function (\Exception $e) {
				$this->emit('error', $e);
			});
		}

		new CommandsDispatcher($this);
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
			case 'dialogflow':
			return $this->dialogflow;
			break;
			case 'pool':
			return $this->pool;
			break;
		}

		return parent::__get($name);
	}

	/**
     * @return string
     * @internal
     */
	function serialize(): string
	{
		$pool = $this->pool;
		$this->pool = null;

		$str = parent::serialize();
		$this->pool = $pool;

		return $str;
	}

	/**
     * @return \React\Promise\ExtendedPromiseInterface|null
     * @internal
     */
	function eval(string $code, array $options = array()): ExtendedPromiseInterface
	{
		return (new Promise(function (callable $resolve, callable $reject) use ($code) {
			if(!(UniversalHelpers::isValidPHP($code))) return $reject(new InvalidArgumentException('Code given is not a valid php code'));
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

			if(!($result instanceof PromiseInterface)) {
				return $resolve($result);
			}

			return $result;
		}));
	}

	function addEventTimer($time, callable $listener, string $event)
	{
		if(!(is_int($time) || is_float($time))) throw new InvalidArgumentException('Time must be int or float');

		$this->on($event, function () use ($time, $listener) { $this->addTimer($time, $listener); });
	}

	function addEventPeriodicTimer($time, callable $listener, string $event)
	{
		if(!(is_int($time) || is_float($time))) throw new InvalidArgumentException('Time must be int or float');

		$this->on($event, function () use ($time, $listener) { $this->addPeriodicTimer($time, $listener); });
	}

	/**
	 * Validates the passed config.
	 * @param array  $config
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	protected function validateConfigs(array $config): void
	{
		$validator = Validator::make($config, array(
			'eventHandler.class' => 'class:\\Animeshz\\ClusterPlus\\Dependent\\EventHandler,string_only',
			'provider.class' => 'class:\\CharlotteDunois\\Livia\\Providers\\MySQLProvider,string_only',
			'dialogflow' => 'string'
		));

		if($validator->fails()) {
			$errors = $validator->errors();

			$name = \array_keys($errors)[0];
			$error = $errors[$name];

			throw new \InvalidArgumentException('Client Option '.$name.' '.\lcfirst($error));
		}
	}
}