<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/


use \Animeshz\ClusterPlus\API\DialogFlow\Models\QueryInput;
use \Animeshz\ClusterPlus\API\DialogFlow\Models\TextInput;

return function(\Animeshz\ClusterPlus\Client $client) {
	return (new class($client) extends \Animeshz\ClusterPlus\Dependent\Command {
		function __construct($client) {
			parent::__construct($client, [
				'name' => 'talk',
				'group' => 'ai',
				'description' => 'Talk to me!',
				'args' => [
					[
						'key' => 'request',
						'prompt' => 'Ask me anything, I will try my best to answer you.',
						'type' => 'string'
					]
				]
			]);
		}

		function threadRun(\CharlotteDunois\Livia\CommandMessage $message, \ArrayObject $args, bool $fromPattern)
		{
			$answer = $this->client->dialogflow->getAnswer($args['request'], $message->author->id);
			$answer->done(function (\Animeshz\ClusterPlus\API\DialogFlow\Models\Answer $answer) use ($message) {
				$message->say($answer);
			}, function (\Exception $e) {
				$this->client->dialogflow->handlePromiseRejection($e);
			});

			// $dialogflow = $this->client->dialogflow;

			// $answer = $dialogflow->api->endpoints->sessions->detectIntent($dialogflow->project['project_id'], $message->message->author->id, new QueryInput(new TextInput($args['request'])))->then(function ($data) use ($message, $dialogflow)
			// {
			// 	// var_dump($data);
			// 	$ans = new \Animeshz\ClusterPlus\API\DialogFlow\Models\Answer($dialogflow, $data);
			// 	return $message->say($ans);
			// })->otherwise(function (\Exception $e) {
			// 	echo $e->getMessage();
			// });
		}
	});
};