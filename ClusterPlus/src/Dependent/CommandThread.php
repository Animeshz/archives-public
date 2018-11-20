<?php
/**
 * ClusterPlus
 * Copyright 2018 Animeshz, All Rights Reserved
 *
 * License: https://github.com/Animeshz/ClusterPlus/blob/master/LICENSE
*/

namespace Animeshz\ClusterPlus\Dependent;

use \CharlotteDunois\Phoebe\AsyncTask;

/**
 * Represents a Command thread.
 */
class CommandThread extends AsyncTask
{
    /**
     * The command name.
     * @var string
     */
    public $cmdname;
    
    /**
     * The command method to invoke.
     * @var string
     */
    public $method;
    
    /**
     * The command arguments.
     * @var string
     */
    public $args;
    
    /**
     * Constructor.
     * @param string  $cmdname
     * @param string  $method
     * @param array   $args
     */
    function __construct(string $cmdname, string $method, array $args) {
        parent::__construct();
        
        $this->cmdname = $cmdname;
        $this->method = $method;
        
        $this->args = \serialize($args);
    }
    
    /**
     * Runs the command.
     * @return void
     */
    function run() {
        $this->wrap(function () {
            $client = \Animeshz\ClusterPlus\Dependent\Worker::$client;
            
            $args = \unserialize($this->args);
            $cmd = $client->registry->resolveCommand($this->cmdname);
            if(is_null($cmd)) $cmd = $client->collector->commands->resolve($args[0]->guild, $this->cmdname);
            if(is_null($cmd)) $client->handlePromiseRejection(new \Exception("Unable to find {$this->cmdname} in {$args[0]->guild->name}"));
            
            return $cmd->threadRun(...$args);
        });
    }
}