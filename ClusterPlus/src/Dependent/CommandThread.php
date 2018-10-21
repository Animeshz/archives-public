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
class CommandThread extends AsyncTask {
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
            
            $cmd = $client->registry->resolveCommand($this->cmdname);
            $args = \unserialize($this->args);
            
            return $cmd->threadRun(...$args);
        });
    }
}