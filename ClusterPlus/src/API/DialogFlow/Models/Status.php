<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\API\DialogFlow\Models;

use \Animeshz\ClusterPlus\API\DialogFlow\DialogFlowClient;

class Status extends ClientBase
{
	/**
	 * @var int
	 */
	protected $code;

	/**
	 * @var array
	 */
	protected $details;

	/**
	 * @var string
	 */
	protected $message;

	function __construct(DialogFlowClient $dialogflow, array $answer)
	{
		parent::__construct($dialogflow);
		$this->_patch($answer);
	}

	/**
	 * @internal
	 * @return void
	 */
	function _patch(array $answer): void
	{
		$this->code = $answer['code'];
		$this->details = $answer['details'] ?? $this->details;
		$this->message = $answer['message'] ?? $this->message;
	}
}