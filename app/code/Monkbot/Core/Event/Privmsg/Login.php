<?php
namespace Monkbot\Core\Event\Privmsg;

use Monkbot\Framework\Db;
use Monkbot\Framework\Event\AbstractEvent;

class Login extends AbstractEvent {
    public function execute() {
        $user = $this->getUser();

        if(!$this->getInChannel() && !$user->getLoginUsername()) {
            if($this->getCommand() == 'LOGIN') {
                $username = $this->getCommandParams()[0] ?? null;
                $password = $this->getCommandParams()[1] ?? null;

                if(!$username || !$password) {
                    $this->notice($this->getUser()->getNick(), 'Syntax: LOGIN <username> <password>');
                }
                else {
                    $dbUser = null;

                    if(Db::getInstance()->getDb()->table('user')->count()) {
                        $dbUser = Db::getInstance()->getDb()->table('user')->where('username', $username)->limit(1)->fetch();
                    }

                    if($dbUser) {
                        if(password_verify($password,$dbUser['password'])) {
                            $user->setLoginUsername($dbUser['username']);
                            $this->notice($this->getUser()->getNick(), 'You are now logged in');
                            return;
                        }
                    }

                    $this->notice($this->getUser()->getNick(), 'Invalid login specified');
                }
            }
        }
    }
}