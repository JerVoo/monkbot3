<?php
namespace Monkbot\Framework;

class UserRegistry {
    private $_users = [];
    private static $_instance;

    public static function getInstance() {
        if(!self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function create($nickname, $username, $address, $gecos) {
        $user = new User();
        $user->setNick($nickname);
        $user->setNickname($nickname);
        $user->setIdent($username);
        $user->setUsername($username);
        $user->setAddress($address);
        $user->setChost($address);
        $user->setGecos($gecos);

        $this->_users[] = &$user;

        return $user;
    }

    public function find($nickname) {
        foreach($this->_users as &$user) {
            if($user->getNickname() == $nickname) {
                return $user;
            }
        }
    }
}