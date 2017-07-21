<?php
namespace Monkbot\Core\Event\Privmsg;

use Monkbot\Framework\Db;
use Monkbot\Framework\Event\AbstractEvent;

class Set extends AbstractEvent {
    public function execute() {
        $user = $this->getUser();

        if(!$this->getInChannel() && $user->getLoginUsername() && $this->getCommand() == 'SET') {
            if(strtoupper($this->getCommandParams()[0]) == 'PASSWORD') {
                if(!$this->getCommandParams()[1]) {
                    $this->notice($this->getNick(), 'Syntax: SET PASSWORD <new-password>');
                }
                else {
                    $user = $this->getUser();
                    $dbUser = Db::getInstance()->getDb()->table('user')->where('username', $user->getUsername())->fetch();
                    $dbUser->update([
                        'password'      => password_hash($this->getCommandParams()[1], PASSWORD_BCRYPT)
                    ]);

                    $this->notice($this->getNick(), 'Your password has been updated.');
                }
            }
        }
    }
}