<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
 */

namespace Animeshz\ClusterPlus\Models;

use CharlotteDunois\Yasmin\Models\Guild;

/**
 * Invite Storage
 */
class InviteStorage extends Storage
{
	function resolve($guild, string $name): ?Invite
	{
		if ($guild instanceof Guild) $guild = $guild->id;

		if ($this->has($guild))
		{
			$context = $this->get($guild);

			if ($context->has($name))
			{
				return $context->get($name);
			}
		}

		return null;
	}

	/**
	 * @param array $invites
	 * @param bool  $update
	 * @return void
	 */
	function store(array $invites, bool $update = false): void
	{
		foreach ($invites as $invite)
		{
			$guildID = $invite->guild->id;
			if (!$this->has($guildID)) $this->set($guildID, new Storage($this->client));
			$inv = $this->get($guildID);
			$inv->set($invite->inviter->id, $invite);

			if ($update)
			{
				$c = (array)json_decode(json_encode($invite));
				$c = array_filter($c, function ($value, $key)
				{
					return $key !== 'guild';
				});

				$dbCmds = $this->client->provider->get($invite->guild, 'commands', []);
				$dbCmds[] = $c;
				$this->client->provider->set($invite->guild, 'commands', $dbCmds);
			}
		}
	}
}