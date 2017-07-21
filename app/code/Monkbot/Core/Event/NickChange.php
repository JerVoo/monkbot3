<?php
namespace Monkbot\Core\Event;

use Monkbot\Framework\Event\AbstractEvent;
use Monkbot\Framework\Registry;
use Monkbot\Framework\UserRegistry;
use Monkbot\Framework\Log;

class NickChange extends AbstractEvent {
    public function execute() {
        $nick = $this->getNick();
        $newnick = $this->getParts()[2];

        if(substr($newnick, 0 ,1) == ':') {
            $newnick = substr($newnick, 1);
        }

        if($nick == Registry::getInstance()->getNickname()) {
            Registry::getInstance()->setNickname($nick);
        }

        Log::write('debug', 'Nickname for NICK event: ' . $nick);
        if($user = UserRegistry::getInstance()->find($nick)) {
            $user->setNickname($newnick);
            $user->setNick($newnick);

            Log::write('debug', $user->getNickname() . '!' . $user->getUsername() . '@' . $user->getAddress() . ' changed nick');
        }
    }
}