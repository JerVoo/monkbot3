<?php
namespace Monkbot\Framework;

class Event {
    public static function dispatch($name, $data) {
        Log::write(Log::DEBUG, 'Dispatching event ' . $name);
        return Registration::dispatchEvent($name, $data);
    }
}