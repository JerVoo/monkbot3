<?php
namespace Monkbot\Framework;

use Monkbot\Framework\Network\Network;

class Starter {
    private $_networkPids = [];

    public function start() {
        Log::write(Log::DEBUG, 'Finding all networks from configuration');

        $networks = Config::getInstance()->get('networks')->getKeys();

        Log::write(Log::DEBUG, 'Found ' . count($networks) . ' networks');

        foreach($networks as $network) {
            $networkConfig = Config::getInstance()->get('networks/' . $network);

            if(function_exists('pcntl_fork')) {
                $pid = pcntl_fork();
                if(!$pid) {
                    Log::write(Log::DEBUG, 'Forked into background, process ID ' . getmypid());
                    $network = new Network($network, $networkConfig);
                    $network->connect();
                }
            }
        }

        foreach($this->_networkPids as $pid) {
            pcntl_waitpid($pid);
        }
    }
}