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
use CharlotteDunois\Collect\Collection;

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
			$collection = $this->get($guild);

			if ($collection->has($name)) {
				return $collection->get($name);
			} else {
				$found = $collection->keys()->filter(function ($key) use ($name) {
					return mb_stripos($key, $name);
				});

				$count = $found->count();
				if ($count === 1) {
					return $found->first();
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
			if(!$this->has($guildID)) $this->set($guildID, new Collection);
			$cmd = $this->get($guildID);
			$cmd->set($module->name, $module);
		}
	}
}