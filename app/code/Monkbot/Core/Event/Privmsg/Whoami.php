<?php
namespace Monkbot\Core\Event\Privmsg;

use Monkbot\Framework\Db;
use Monkbot\Framework\Event\AbstractEvent;

class Whoami extends AbstractEvent
{
    public function execute()
    {
        $user = $this->getUser();

        if (!$this->getInChannel() && $this->getCommand() == 'WHOAMI') {
            $this->notice($this->getNick(), 'Hi, I\'ve recognized you as being ' . $this->getUser()->getNick() . '!' . $this->getUser()->getUsername() . '@' . $this->getUser()->getAddress());

            if($this->getUser()->getLoginUsername()) {
                $this->notice($this->getNick(), 'You have logged in as user ' . $this->getUser()->getLoginUsername() . ' with level '.  $this->getUser()->getLevel());
            }
            else {
                $this->notice($this->getNick(), 'You have logged in as Guest with level '.  $this->getUser()->getLevel());
            }
        }
    }
}