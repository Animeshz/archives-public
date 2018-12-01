<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Models;

use Animeshz\ClusterPlus\Exceptions\MultipleEntryFoundException;
use CharlotteDunois\Yasmin\Models\Guild;

/**
 * Module Storage
 */
class ModuleStorage extends Storage
{
	/**
	 * Resolves instance of module by guild and module name.
	 * 
	 * @param string|CharlotteDunois\Yasmin\Models\Guild	$guild	Guild in which to fetch module
	 * @param string										$name	Name of the module
	 * @return Animeshz\ClusterPlus\Models\Command|null
	 * @throws Animeshz\ClusterPlus\Exceptions\MultipleEntryFoundException
	 */
	function resolve($guild, string $name): ?Module
	{
		if ($guild instanceof Guild) $guild = $guild->id;

		if($this->has($guild)) {
			$context = $this->get($guild);

			if ($context->has($name)) {
				return $context->get($name);
			} else {
				$found = $context->keys()->filter(function ($key) use ($name) {
					return mb_stripos($key, $name);
				});

				$count = $found->count();
				if ($count === 1) {
					return $context->get($found->first());
				} elseif ($count > 1) {
					throw new MultipleEntryFoundException("Multiple Modules Found: Try to be more specific");
				}
			}
		}

		return null;
	}

	function store(array $modules): void
	{
		foreach ($modules as $module) {
			$guildID = $module->guild->id;
			if(!$this->has($guildID)) $this->set($guildID, new Storage($this->client));
			$cmd = $this->get($guildID);
			$cmd->set($module->name, $module);
		}
	}
}