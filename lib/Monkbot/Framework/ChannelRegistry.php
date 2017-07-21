<?php
namespace Monkbot\Framework;

class ChannelRegistry {
    private $_channels = [];
    private static $_instance;

    public static function getInstance() {
        if(!self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function create($name) {
        $channel = new Channel();
        $channel->setName($name);

        $this->_channels[$name] = $channel;
    }

    public function find($name) {
        return $this->_channels[$name] ?? null;
    }
}