<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

use \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient;
use \CharlotteDunois\Yasmin\Interfaces\StorageInterface;
use \CharlotteDunois\Collect\Collection;

class Storage extends Collection implements \Serializable, StorageInterface
{
	/**
	 * @var \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient
	 */
	protected $dialogflow;
	
	/**
	 * @internal
	 */
	function __construct(DialogFlowClient $dialogflow, array $data = null) {
		parent::__construct($data);
		$this->dialogflow = $dialogflow;
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
		unset($vars['dialogflow']);
		return \serialize($vars);
	}
	
	/**
	 * @return void
	 * @internal
	 */
	function unserialize($data): void
	{
		if(ClientBase::$serializeDialogflow === null) {
			throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
		}

		$data = \unserialize($data);
		foreach($data as $name => $val) {
			$this->$name = $val;
		}

		$this->dialogflow = ClientBase::$serializeDialogflow;
	}

	/**
	 * {@inheritdoc}
	 * @return \CharlotteDunois\Yasmin\Interfaces\StorageInterface
	 */
	function copy(): StorageInterface
	{
		return (new static($this->dialogflow, $this->data));
	}

		/**
	 * {@inheritdoc}
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
		function delete($key): StorageInterface
		{
			$this->keyValidator($key);

			$key = (string) $key;
			parent::delete($key);
			$this->dialogflow->emit('internal.storage.delete', $this, $key);

			return $this;
		}

	/**
	 * {@inheritdoc}
	 * @return mixed|null
	 * @throws \InvalidArgumentException
	 */
	function get($key)
	{
		$this->keyValidator($key);

		$key = (string) $key;
		return parent::get($key);
	}
	
	/**
	 * {@inheritdoc}
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	function has($key): bool
	{
		$this->keyValidator($key);

		$key = (string) $key;
		return parent::has($key);
	}
	
	/**
	 * {@inheritdoc}
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	function set($key, $value): StorageInterface
	{
		$this->keyValidator($key);

		$key = (string) $key;
		parent::set($key, $value);
		$this->dialogflow->emit('internal.storage.set', $this, $key, $value);

		return $this;
	}

	private function keyValidator($key) {
		if(\is_array($key) || \is_object($key)) {
			throw new \InvalidArgumentException('Key can not be an array or object');
		}
	}
}