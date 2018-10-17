<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Commands;

class CommandsDispatcher
{
	/**
	 * @var \CharlotteDunois\Livia\LiviaClient<\CharlotteDunois\Yasmin\Client>
	 */
	protected $client;

	public function __construct(\CharlotteDunois\Yasmin\Client $client)
	{
		$this->client = $client;

		$this->client->registry->registerDefaults();
		$this->registerGroups()->registerCommands();

		$this->dispatchCustomCommands();
	}

	public function registerGroups()
	{
		$this->client->registry->registerGroup(
			(new \CharlotteDunois\Livia\Commands\CommandGroup($this->client, 'moderation', 'Cluster Plus Moderation', true)),
			(new \CharlotteDunois\Livia\Commands\CommandGroup($this->client, 'polls', 'Cluster Plus Polls')),
			(new \CharlotteDunois\Livia\Commands\CommandGroup($this->client, 'forms', 'Cluster Plus Forms')),
			(new \CharlotteDunois\Livia\Commands\CommandGroup($this->client, 'meta', 'Cluster Plus Meta'))
		);
		return $this;
	}

	public function registerCommands()
	{
		$this->client->registry->registerCommandsIn(__DIR__.'/commands', true);
		return $this;
	}

	function dispatchCustomCommands()
	{
		$this->client->on('message', function (\CharlotteDunois\Yasmin\Models\Message $message)
		{
			$prefix = $this->client->getGuildPrefix($message->guild);
			$pattern = empty($this->client->dispatcher->commandPatterns[$prefix]) ? $this->client->dispatcher->buildCommandPattern($prefix) : $this->client->dispatcher->commandPatterns[$prefix];
			$command = $this->matchCommand($message, $pattern, 2);

			if($command instanceof \Animeshz\ClusterPlus\Models\Command) $command->run($message);
		});
	}

	protected function matchCommand(\CharlotteDunois\Yasmin\Models\Message $message, string $pattern, int $commandNameIndex = 1)
	{
		global $collector;

		\preg_match($pattern, $message->content, $matches);
		if(!empty($matches)) {
			return $collector->commands->first(function (\Animeshz\ClusterPlus\Models\Command $command) use ($matches, $commandNameIndex) { return (strpos($command->name, $matches[$commandNameIndex]) !== false); });
		}

		return null;
	}
}