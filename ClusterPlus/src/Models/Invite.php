<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Models;

use Animeshz\ClusterPlus\Client;
use CharlotteDunois\Collect\Collection;
use CharlotteDunois\Yasmin\Models\ClientBase;
use CharlotteDunois\Yasmin\Models\User;
use React\Promise\ExtendedPromiseInterface;
use Recoil\React\ReactKernel;

use function \React\Promise\all;
use function \React\Promise\resolve;
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
	 * @internal
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

		$this->client = ClientBase::$serializeClient;
		$this->guild = $this->client->guilds->resolve($vars['guild']);

		ReactKernel::start(function () use ($vars) {
			$this->inviter = yield $this->client->fetchUser($vars['inviter']);
			$invited = [];
			foreach ($vars['invited'] as $userid) {
				$invited[] = yield $this->client->fetchUser($userid);
			}
			$this->invited = new Collection($invited);
		}, $this->client->loop);
	}

	/**
	 * Recreates instances of Invite which are json decoded
	 * @param \Animeshz\ClusterPlus\Client	$client		Client instance
	 * @param array										$vars json decoded array of this object
	 * @return self
	 */
	static function jsonUnserialize(Client $client, array $vars): self
	{
		$vars['guild'] = $client->guilds->resolve($vars['guild']);
		ReactKernel::start(function () use (&$vars) {
			$vars['inviter'] = yield $this->client->fetchUser($vars['inviter']);
			$invited = [];
			foreach ($vars['invited'] as $userid) {
				$invited[] = yield $this->client->fetchUser($userid);
			}
			$this->invited = new Collection($invited);
		});

		return new static($client, $vars);
	}

	/**
	 * Creates a new instances of Invite by Yasmin's invite model
	 * @param \Animeshz\ClusterPlus\Client				$client		Client instance
	 * @param \CharlotteDunois\Yasmin\Models\Invite 	$invite		Yasmin's Invite Instance
	 * @return self
	 */
	static function make(Client $client, \CharlotteDunois\Yasmin\Models\Invite $invite): self
	{
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
	}

	/**
	 * @internal
	 * @param array $info
	 * @return void
	 */
	function _patch(array $info): void
	{
		$this->guild = $info['guild'];

		if (is_string($info['inviter'])) {
			ReactKernel::start(function () use (&$info) {
				$this->inviter = yield $this->client->fetchUser($info['inviter']);
			});
		} elseif ($info['inviter'] instanceof User) {
			$this->inviter = $info['inviter'];
		} else {
			throw new \InvalidArgumentException("inviter must be an instance of User or a string");
		}

		if (isset($info['invited'])) {
			ReactKernel::start(function () use ($info) {
				if (!$info['invited'] instanceof Collection) {
					$info['invited'] = new Collection($info['invited']);
				}
				$promisedInvited = $info['invited']->map(function ($user) {
					if(is_string($user)) return $this->client->fetchUser($user);
					if($user instanceof User) return resolve($user);
				})->all();
				$invited = yield all($promisedInvited);				
				$this->invited = isset($this->invited) ? $this->invited->merge($invited) : new Collection($invited);
			});
		} else {
			$this->invited = new Collection;
		}
	}
}