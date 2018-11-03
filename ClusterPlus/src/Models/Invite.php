<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Models;

use \Animeshz\ClusterPlus\Client;
use \CharlotteDunois\Collect\Collection;
use \CharlotteDunois\Yasmin\Models\ClientBase;
use \CharlotteDunois\Yasmin\Models\User;
use function \React\Promise\all;
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
class Invite implements \JsonSerializable, \Serializable
{
	/**
	 * The client which initiated the instance.
	 * @var \Animeshz\ClusterPlus\Client
	 */
	protected $client;

	/**
     * The invite code.
     * @var string
     */
    protected $code;

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
	 * A collection of User instances which are invited by $inviter
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
	function __construct(Client $client, array $info)
	{
		$this->client = $client;

		$this->_patch($info);
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
		$vars['invited'] = $vars['invited']->map(function ($user)
		{
			return $user->id;
		})->all();

		return \serialize($vars);
	}

	/**
	 * @return array
	 * @internal
	 */
	public function jsonSerialize(): array
	{
		$vars = \get_object_vars($this);
		unset($vars['client']);
		$vars['guild'] = $vars['guild']->id;
		$vars['inviter'] = $vars['inviter']->id;
		$vars['invited'] = $vars['invited']->map(function ($user)
		{
			return $user->id;
		})->all();

		return $vars;
	}
	
	/**
	 * @return void
	 * @internal
	 */
	function unserialize($vars): void
	{
		if(ClientBase::$serializeClient === null) {
			throw new \Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
		}
		
		$vars = \unserialize($vars);

		$this->client = \CharlotteDunois\Yasmin\Models\ClientBase::$serializeClient;
		$this->guild = $this->client->guilds->resolve($vars['guild']);
		$this->inviter = $this->client->fetchUser($vars['inviter']);
		$promisedInvited = (new Collection($vars['invited']))->map(function ($userid) {
			return $this->client->fetchUser($userid);
		})->all();

		all($promisedInvited)->then(function ($userFetched) {
			$this->invited = new Collection($userFetched);
		});
	}

	static function jsonUnserialize($client, $vars)
	{
		$vars['guild'] = $client->guilds->resolve($vars['guild']);
		$vars['inviter'] = $client->fetchUser($vars['inviter']);

		return new static($client, $vars);
	}

	static function make(Client $client, \CharlotteDunois\Yasmin\Models\Invite $invite): Invite
	{
		$info['code'] = $invite->code;
		$info['guild'] = $invite->guild;
		$info['inviter'] = $invite->inviter;

		return new static($client, $info);
	}

	/**
	 * @internal
	 * @param \CharlotteDunois\Yasmin\Models\User ...$invited
	 * @return void
	 */
	function _patchUser(User ...$invited): void
	{
		foreach ($invited as $i) {
			$this->invited->set($this->invited->count(), $i);
		}
		
		//update to database
		$oldData = $this->client->provider->get($this->guild, 'invites', []);
		$data[$this->code] = $this;
		$this->client->provider->set($this->guild, $data);
	}

	/**
	 * @internal
	 * @param array $info
	 * @return void
	 */
	function _patch(array $info): void
	{
		$this->code = $info['code'];
		$this->guild = $info['guild'];
		$this->inviter = $info['inviter'];
		$this->invited = (isset($info['invited']) ? ($info['invited'] instanceof Collection ? $info['invited'] : new Collection($info['invited'])) : new Collection);

		//update to database
		$data = $this->client->provider->get($this->guild, 'invites', []);
		$data[$this->code] = $this;
		$this->client->provider->set($this->guild, 'invites', $data);
	}
}