<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Models;

use \Animeshz\ClusterPlus\Client;
use \CharlotteDunois\Validation\Validator;
use \CharlotteDunois\Yasmin\Models\ClientBase;
use \CharlotteDunois\Yasmin\Models\Message;

/**
 * A command that can be run in a client.
 *
 * @property \CharlotteDunois\Livia\LiviaClient                 $client             The client which initiated the instance.
 * @property string                                             $name               The name of the command.
 * @property string[]                                           $aliases            Aliases of the command.
 * @property string                                             $description        A short description of the command.
 * @property string[]                                           $examples           Examples of and for the command.
 * @property string[]|null                                      $userPermissions    The required permissions for the user to use the command.
 */
class Module implements \JsonSerializable, \Serializable
{
	const ATTACHABLE_TO = [
		'timer',
		'periodicTimer',
		'command'
	];

	const TIMER = 'Send the timer duration in second.'.\PHP_EOL;
	const PEREODICTIMER = 'Send the periodicTimer duration in second.'.\PHP_EOL;
	const COMMAND = 'Send the name of fancy command you like.'.\PHP_EOL;

	/**
	 * The client which initiated the instance.
	 * @var \CharlotteDunois\Livia\LiviaClient
	 */
	protected $client;
	
	/**
	 * The name of the module.
	 * @var string
	 */
	protected $name;
	
	/**
	 * A short description of the module.
	 * @var string
	 */
	protected $description;

	/**
	 * Guild which module belong to
	 * @var \CharlotteDunois\Yasmin\Models\Guild
	 */
	protected $guild;

	/**
	 * Input of module-creator.
	 * @var string
	 */
	protected $input;

	/**
	 * Constructs a new Module. Info is an array as following:
	 *
	 * ```
	 * array(
	 *   'name' => string,
	 *   'guild' => \CharlotteDunois\Yasmin\Models\Guild,
	 *   'description => string,
	 * )
	 * ```
	 *
	 * @param \CharlotteDunois\Livia\LiviaClient    $client
	 * @param array                                 $info
	 * @throws \InvalidArgumentException
	 */
	function __construct(Client $client, array $info)
	{
		$this->client = $client;
		
		$validator = Validator::make($info, array(
			'name' => 'required|string|lowercase|nowhitespace',
			'description' => 'required|string',
			'guild' => 'required|class:\\CharlotteDunois\\Yasmin\\Models\\Guild',
			'input' => 'required|string',
		));
		
		try {
			$validator->throw();
		} catch (\RuntimeException $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
		
		$this->name = $info['name'];
		$this->description = $info['description'];
		$this->guild = $info['guild'];
		$this->input = $info['input'];
	}
	
	/**
	 * @param string  $name
	 * @return bool
	 * @throws \Exception
	 * @internal
	 */
	function __isset($name)
	{
		try {
			return $this->$name !== null;
		} catch (\RuntimeException $e) {
			if($e->getTrace()[0]['function'] === '__get') {
				return false;
			}
			
			throw $e;
		}
	}
	
	/**
	 * @param string  $name
	 * @return mixed
	 * @throws \RuntimeException
	 * @internal
	 */
	function __get($name)
	{
		if(\property_exists($this, $name)) {
			return $this->$name;
		}		
		throw new \RuntimeException('Unknown property '.\get_class($this).'::$'.$name);
	}
	
	/**
	 * @return string
	 * @internal
	 */
	function serialize()
	{
		$vars = \get_object_vars($this);
		unset($vars['client'], $vars['code']);
		return \serialize($vars);
	}

	/**
	 * @return array
	 * @internal
	 */
	public function jsonSerialize()
	{
		$vars = \get_object_vars($this);
		$vars['guild'] = ($vars['guild'])->id;
		unset($vars['client'], $vars['code']);

		return $vars;
	}
	
	/**
	 * @return void
	 * @internal
	 */
	function unserialize($vars) {
		if(ClientBase::$serializeClient === null) {
			throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
		}
		
		$vars = \unserialize($vars);
		foreach($vars as $name => $val) {
			$this->$name = $val;
		}
		
		$this->client = ClientBase::$serializeClient;
		$this->guild = $this->client->guilds->resolve($this->guild);
	}

	static function jsonUnserialize($client, $vars)
	{
		$vars['guild'] = $client->guilds->resolve($vars['guild']);

		return new self($client, $vars);
	}

	protected function createCode()
	{
		//do something with $this->input
		//algorithmic base
	}

	function runByCommand(Message $message): void
	{
		$this->run('command', ['message' => $message]);
	}

	/**
	 * runs the module
	 * @param string $type 
	 * @param array $args 
	 * @return void
	 */
	protected function run(string $type, array $args): void
	{
		foreach($args as $key=>$value){ $$key = $value; }
		$code = $this->createCode();
		safe_eval($code);
		flush($code);
	}
}