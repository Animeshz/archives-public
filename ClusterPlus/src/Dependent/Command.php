<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Dependent;

use \CharlotteDunois\Livia\CommandMessage;

/**
 * The job of this class is to provide a way to effort-less invoke a command in a thread.
 */
class Command extends \CharlotteDunois\Livia\Commands\Command {    
	/**
	 * Invokes the `threadRun` method in a new thread. This method must be overridden, unless you only need the `threadRun` method.
	 * @return \React\Promise\ExtendedPromiseInterface
	 */
	function run(CommandMessage $message, \ArrayObject $args, bool $fromPattern) {
		return $this->client->pool->runCommand($this->name, 'threadRun', $message, $args, $fromPattern);
	}
	
	/**
	 * This method must be overridden if you actually want to run this in a thread (letting it run `SarahCommand::run`).
	 * @return \React\Promise\ExtendedPromiseInterface
	 * @throws \LogicException  Thrown when the method is not overridden and the method gets invoked.
	 */
	function threadRun(CommandMessage $message, \ArrayObject $args, bool $fromPattern) {
		throw new \LogicException('The "threadRun" method of the '.$this->name.' command is not implemented');
	}
}