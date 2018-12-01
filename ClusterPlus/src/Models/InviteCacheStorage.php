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
 * InviteCache Storage
 */
class InviteCacheStorage extends Storage
{
	function resolve($guild, string $name): ?\CharlotteDunois\Yasmin\Models\Invite
	{
		if ($guild instanceof Guild) $guild = $guild->id;

		if($this->has($guild)) {
			$context = $this->get($guild);

			if ($context->has($name)) {
				return $context->get($name);
			}
		}

		return null;
	}

	function store(array $invites): void
	{
		foreach ($invites as $invite) {
			$guildID = $invite->guild->id;
			if(!$this->has($guildID)) $this->set($guildID, new Storage($this->client));
			$invc = $this->get($guildID);
			$invc->set($invite->code, $invite);
		}
	}
}