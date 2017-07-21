<?php
namespace Monkbot\Core\Event;

use Monkbot\Framework\Event\AbstractEvent;
use Monkbot\Framework\Log;
use Monkbot\Framework\UserRegistry;
use Monkbot\Framework\Registry;

class JoinHandler extends AbstractEvent {
    public function execute() {
        Log::write('debug', 'Join found for ' . $this->getNick() . ' in ' . $this->getChannel());

        $this->write('WHO ' . $this->getChannel());
    }

    public function parseWho() {
        $parts = $this->getParts();

        $username = $parts[4];
        $address  = $parts[5];
        $server   = $parts[6];
        $nick     = $parts[7];

        $gecos = [];
        for($i = 10; $i < count($parts); $i++) {
            $gecos[] = $parts[$i];
        }

        $gecos = implode(' ', $gecos);

        if(!UserRegistry::getInstance()->find($nick)) {
            $user = UserRegistry::getInstance()->create(
                $nick,
                $username,
                $address,
                $gecos
            );
            $user->setServer($server);

            Log::write('debug', 'Created user for nick ' . $nick);
        }
    }
}