<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

use \CharlotteDunois\Collect\Collection;

class ContextStorage extends Collection
{
	/**
	 * Factory to create (or retrieve existing) users.
	 * @param array  $data
	 * @param bool   $userFetched
	 * @return \Animeshz\ClusterPlus\API\DialogFlow\Models\QueryResult
	 * @internal
	 */
	function factory(array $data) {
		if(parent::has($data['id'])) {
			$user = parent::get($data['id']);
			$user->_patch($data);
			return $user;
		}
		
		$user = new \CharlotteDunois\Yasmin\Models\User($this->client, $data, false, $userFetched);
		$this->set($user->id, $user);
		
		return $user;
	}    
}