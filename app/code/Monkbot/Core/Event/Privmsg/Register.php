<?php
namespace Monkbot\Core\Event\Privmsg;

use Monkbot\Framework\Db;
use Monkbot\Framework\Event\AbstractEvent;

class Register extends AbstractEvent {
    public function execute() {
        $user = $this->getUser();

        if(!$this->getInChannel() && !$user->getLoginUsername() && $this->getCommand() == 'REGISTER') {
            $username = $this->getCommandParams()[0] ?? null;
            $password = $this->getCommandParams()[1] ?? null;

            if(!$username || !$password) {
                $this->notice($this->getUser()->getNick(), 'Syntax: REGISTER <username> <password>');
                return;
            }

            $dbUser = null;
            if(Db::getInstance()->getDb()->table('user')->count()) {
                $dbUser = Db::getInstance()->getDb()->table('user')->where('username', $username)->limit(1)->fetch();
            }

            if(!$dbUser) {
                $level = 1;

                if(Db::getInstance()->getDb()->table('user')->count() == 0) {
                    $level = 9999;
                }

                Db::getInstance()->getDb()->table('user')->insert(
                    [
                        'username'          => $username,
                        'password'          => password_hash($password, PASSWORD_BCRYPT),
                        'level'             => $level
                    ]
                );

                $this->notice($this->getUser()->getNick(), 'You have succesfully registered!');
                if($level == 9999) {
                    $this->notice($this->getUser()->getNick(), 'You\'re the first user of the bot and are thus an admin now.');
                }

                $this->getUser()->setLoginUsername($username);
            }
        }
    }
}