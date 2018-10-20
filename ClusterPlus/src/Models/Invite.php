<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Models;

use \CharlotteDunois\Collect\Collection;
use \CharlotteDunois\Yasmin\Models\User;
/**
 * Invite 
 *
 * @property \CharlotteDunois\Livia\LiviaClient                 $client             The client which initiated the instance.
 * @property string                                             $name               The name of the command.
 * @property string[]                                           $aliases            Aliases of the command.
 * @property string                                             $description        A short description of the command.
 * @property string[]                                           $examples           Examples of and for the command.
 * @property string[]|null                                      $userPermissions    The required permissions for the user to use the command.
 */
abstract class Command implements \JsonSerializable, \Serializable
{
	/**
	 * The client which initiated the instance.
	 * @var \Animeshz\ClusterPlus\Client
	 */
	protected $client;

	/**
	 * Guild which invite belong to
	 * @var \CharlotteDunois\Yasmin\Models\Guild
	 */
	protected $guild;

	/**
	 * User this invite belongs to
	 * @var \CharlotteDunois\Yasmin\Models\User
	 */
	protected $inviter;

	/**
	 * @var \CharlotteDunois\Collect\Collection<\CharlotteDunois\Yasmin\Models\User>
	 */
	protected $invited;
	
	/**
	 * Constructs a new Command. Info is an array as following:
	 *
	 * ```
	 * array(
	 *   'name' => string,
	 *   'aliases' => string[], (optional)
	 *   'description => string,
	 *   'examples' => string[], (optional)
	 *   'userPermissions' => string[], (optional)
	 * )
	 * ```
	 *
	 * @param \CharlotteDunois\Livia\LiviaClient    $client
	 * @param array                                 $info
	 * @throws \InvalidArgumentException
	 */
	function __construct(\CharlotteDunois\Livia\LiviaClient $client, $invite)
	{
		$this->client = $client;

		if ($invite instanceof \CharlotteDunois\Yasmin\Models\Invite) {
			$this->guild = $invite->guild;
			$this->inviter = $invite->inviter;
			$this->invited = new \CharlotteDunois\Collect\Collection;
		} elseif (is_array($invite)) {
			$this->guild = $invite['guild'];
			$this->inviter = $invite['inviter'];
			$this->invited = $invite['invited'];
		}
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
	function serialize(): string
	{
		$vars = \get_object_vars($this);
		unset($vars['client']);
		$vars['guild'] = $vars['guild']->id;
		$vars['inviter'] = $vars['inviter']->id;
		$vars['invited'] = $vars['invited']->all();
		return \serialize($vars);
	}

	/**
	 * @return array
	 * @internal
	 */
	public function jsonSerialize(): array
	{
		$vars = \get_object_vars($this);
		$vars['guild'] = $vars['guild']->id;
		$vars['inviter'] = $vars['inviter']->id;
		$vars['invited'] = $vars['invited']->all();
		unset($vars['client']);

		return $vars;
	}
	
	/**
	 * @return void
	 * @internal
	 */
	function unserialize($vars): void
	{
		if(\CharlotteDunois\Yasmin\Models\ClientBase::$serializeClient === null) {
			throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
		}
		
		$vars = \unserialize($vars);
		foreach($vars as $name => $val) {
			$this->$name = $val;
		}
		
		$this->client = \CharlotteDunois\Yasmin\Models\ClientBase::$serializeClient;
		$this->guild = $this->client->guilds->resolve($this->guild);
		$this->inviter = $this->client->fetchUser($this->inviter);
		$this->invited = new Collection($vars['invited']);
	}

	static function jsonUnserialize($client, $vars)
	{
		$vars['guild'] = $client->guilds->resolve($vars['guild']);
		$vars['inviter'] = $client->fetchUser($vars['inviter']);
		$vars['invited'] = new Collection($vars['invited']);

		return new self($client, $vars);
	}

	/**
	 * Description
	 * @param \CharlotteDunois\Yasmin\Models\User $invited 
	 * @return type
	 */
	function _patch(User $invited)
	{
		$this->invited->set($this->invited->count(), $invited);
		//update to database
	}
}