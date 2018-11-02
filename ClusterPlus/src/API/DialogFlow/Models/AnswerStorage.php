<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

class AnswerStorage extends Storage
{
	/**
	 * Factory to create Answer and store it.
	 * @param array  $data
	 * @return \Animeshz\ClusterPlus\API\DialogFlow\Models\Answer
	 * @internal
	 */
	function factory(array $data): Answer
	{
		$answer = new Answer($this->dialogflow, $data);
		$this->set($answer->responseId, $answer);
		return $answer;
	}
}