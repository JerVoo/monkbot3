<?php
namespace Monkbot\Framework\Event;

use Monkbot\Framework\Network\Server;
use Monkbot\Framework\User;
use Monkbot\Framework\UserRegistry;

abstract class AbstractEvent {
    private $_data = [];
    
    public function __call($methodName, $args) {
        $dataName = $this->fromCamelCase(substr($methodName, 3));

        if(substr($methodName, 0, 3) == 'get') {
            return $this->getData($dataName);
        }

        if(substr($methodName, 0, 3) == 'set') {
            return $this->setData($dataName, $args[0]);
        }

        if(substr($methodName, 0, 3) == 'uns') {
            return $this->unsData($dataName);
        }
    }

    /**
     * Set data in the registry
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null) {
        if(is_array($key)) {
            foreach($key as $k => $v) {
                $this->setData($k, $v);
            }
        }
        else {
            $this->_data[$key] = $value;
        }

        return $this;
    }

    /**
     * Get data from the registry
     * @param string $key
     * @return null
     */
    public function getData(string $key) {
        return $this->_data[$key] ?? null;
    }

    /**
     * Unset registry data
     * @param string $key
     * @return $this
     */
    public function unsData(string $key) {
        if(isset($this->_data[$key])) {
            unset($this->_data[$key]);
        }

        return $this;
    }
    
    public function hasData($key) {
        return isset($this->_data[$key]);
    }

    public function fromCamelCase($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    public function prepare() {
        if($this->hasData('line')) {
            $line = $this->getData('line');

            if(substr($line, 0, 1) == ':') {
                preg_match('/:(.*)!(.*)\@(.*)/is', $this->getParts()[0], $matches);
                if(count($matches) >= 4) {
                    $this->setNick($matches[1]);
                    $this->setNickname($matches[1]);
                    $this->setUsername($matches[2]);
                    $this->setChost($matches[3]);
                    $this->setAddress($matches[3]);

                    $user = UserRegistry::getInstance()->find($this->getNick());

                    if($user) {
                        $user->setUsername($this->getUsername());
                        $user->setIdent($this->getUsername());
                        $user->setChost($this->getChost());
                        $user->setAddress($this->getChost());
                    }
                }
            }

            if(in_array($this->getEvent(), ['PRIVMSG', 'JOIN', 'PART', 'NOTICE'])) {
                if(substr($this->getParts()[2], 0 ,1) == '#') {
                    $this->setChannel($this->getParts()[2]);
                    $this->setTarget($this->getChannel());
                    $this->setInChannel(true);
                }
                else if(substr($this->getParts()[2], 1, 1) == '#') {
                    $this->setChannel(substr($this->getParts()[2], 1));
                    $this->setTarget($this->getChannel());
                    $this->setInChannel(true);
                }
                else {
                    $this->setTarget($this->getParts()[2]);
                }
            }

            if($this->getEvent() == 'PRIVMSG') {
                $textParts = [];
                for($i = 3; $i < count($this->getParts()); $i++) {
                    $textParts[] = $this->getParts()[$i];
                }

                $text = substr(implode(' ', $textParts), 1);

                $this->setText($text);
                $this->setCommand(strtoupper(substr($textParts[0], 1)));

                array_shift($textParts);

                $this->setCommandParams($textParts);
            }
        }
    }

    /**
     * @return User
     */
    public function getUser() {
        return UserRegistry::getInstance()->find($this->getNickname());
    }

    public function msg($target, $text) {
        Server::getInstance()->write('PRIVMSG ' . $target . ' :' . $text);
    }

    public function notice($target, $text) {
        Server::getInstance()->write('NOTICE ' . $target . ' :' . $text);
    }

    public function join($channel, $password = null) {
        Server::getInstance()->write('JOIN ' . $channel . ' :' . $password);
    }

    public function write($data) {
        Server::getInstance()->write($data);
    }

    public function forSelf() {
        return $this->getNick() == Registry::getInstance()->getNickname();
    }
}