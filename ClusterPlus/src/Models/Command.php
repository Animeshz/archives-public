<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Models;

use \Animeshz\ClusterPlus\Client;
use \Animeshz\ClusterPlus\Models\Module;
use \CharlotteDunois\Yasmin\Models\ClientBase;
use \CharlotteDunois\Yasmin\Models\Message;
use \CharlotteDunois\Validation\Validator;

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
class Command implements \JsonSerializable, \Serializable
{
	/**
	 * The client which initiated the instance.
	 * @var \CharlotteDunois\Livia\LiviaClient
	 */
	protected $client;
	
	/**
	 * The name of the command.
	 * @var string
	 */
	protected $name;
	
	/**
	 * A short description of the command.
	 * @var string
	 */
	protected $description;

	/**
	 * Guild which command belong to
	 * @var \CharlotteDunois\Yasmin\Models\Guild
	 */
	protected $guild;
	
	/**
	 * Examples of and for the command.
	 * @var string[]
	 */
	protected $examples = [];

	/**
	 * Modules that will react to the command if it runs
	 * @var Module[]
	 */
	protected $modules = [];
	
	/**
	 * Constructs a new Command. Info is an array as following:
	 *
	 * ```
	 * array(
	 *   'name' => string,
	 *   'guild' => \CharlotteDunois\Yasmin\Models\Guild,
	 *   'description => string,
	 *   'examples' => string[], (optional)
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
			'examples' => 'array:string',
		));
		
		try {
			$validator->throw();
		} catch (\RuntimeException $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
		
		$this->name = $info['name'];
		$this->description = $info['description'];
		$this->guild = $info['guild'];
		$this->examples = $info['examples'] ?? $this->examples;
	}
	
	/**
	 * @param string  $name
	 * @return bool
	 * @throws \Exception
	 * @internal
	 */
	function __isset($name): bool
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
	function serialize(): string
	{
		$vars = \get_object_vars($this);
		unset($vars['client']);
		return \serialize($vars);
	}

	/**
	 * @return array
	 * @internal
	 */
	public function jsonSerialize(): array
	{
		$vars = \get_object_vars($this);
		$vars['guild'] = ($vars['guild'])->id;
		unset($vars['client']);

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

	static function jsonUnserialize(Client $client, array $vars)
	{
		$vars['guild'] = $client->guilds->resolve($vars['guild']);

		return new static($client, $vars);
	}

	function attachModules(Module ...$modules)
	{
		$this->modules = array_merge($this->modules, $modules);
	}
	
	function run(Message $message)
	{
		echo 'running';
		// foreach ($this->modules as $module) {
		// 	//run the module
		// }
	}

}