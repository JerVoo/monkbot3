<?php
namespace Monkbot\Core\Event;

use Monkbot\Framework\Event\AbstractEvent;
use Monkbot\Framework\Log;

class Connect extends AbstractEvent {
    public function connect() {
        Log::write(Log::DEBUG, 'Connection to server successful! ' . $args['server']);
    }
}