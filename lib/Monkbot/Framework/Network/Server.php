<?php
namespace Monkbot\Framework\Network;

use Monkbot\Framework\Config\Item;
use Monkbot\Framework\Event;
use Monkbot\Framework\Log;
use Monkbot\Framework\Registry;

class Server {
    /**
     * @var Item
     */
    private $_networkConfig;

    /**
     * @var string
     */
    private $_host;

    /**
     * @var string
     */
    private $_port;

    /**
     * @var string
     */
    private $_password;

    /**
     * @var Server
     */
    private static $_instance;

    private $_socket;

    public function __construct(Item $networkConfig, string $host, string $port, $password) {
        self::$_instance = $this;
        $this->_networkConfig = $networkConfig;
        $this->_host = $host;
        $this->_port = $port;
        $this->_password = $password;
    }

    /**
     * Return class instance
     * @return self
     */
    public function getInstance() {
        return self::$_instance;
    }

    public function connect() {
        Log::write(Log::DEBUG, 'Creating connection');

        $this->_socket = fsockopen($this->_host, $this->_port);

        if(!$this->_socket) {
            Log::write(Log::DEBUG, 'Could not create connection, moving to next server');
            return;
        }

        fwrite($this->_socket, 'NICK ' . (string) $this->_networkConfig->get('nickname') . PHP_EOL);
        fwrite($this->_socket, 'USER ' . (string) $this->_networkConfig->get('username') . ' 0 0 :' . $this->_networkConfig->get('gecos') . PHP_EOL);

        Event::dispatch('connect',
            [
                'networkConfig'     => $this->_networkConfig,
                'host'              => $this->_host,
                'port'              => $this->_port,
                'password'          => $this->_password,
                'nickname'          => (string) $this->_networkConfig->get('nickname'),
                'username'          => (string) $this->_networkConfig->get('username'),
                'gecos'             => (string) $this->_networkConfig->get('gecos'),
            ]
        );

        while($line = fgets($this->_socket, 1024)) {
            $line  = trim($line);
            $parts = explode(' ', $line);

            Log::write('irc', "\e[1;33m " . $this->_host . ':' . $this->_port . " \e[0;32m<- \e[0m" . $line);

            if($parts[0] == 'PING') {
                $this->write('PONG ' . $parts[1]);
            }

            if($parts[1] == '001') {
                Registry::getInstance()->setNickname($parts[2]);

                Event::dispatch('connect_finished', [
                    'line'      => $line,
                    'parts'     => $parts,
                    'server'    => substr($parts[0], 1)
                ]);

                $channels = $this->_networkConfig->get('channels');
                $keys     = $channels->getKeys();

                foreach($keys as $key) {
                    $channel = (string) $channels->get($key);
                    $this->join($channel);
                }
            }

            $idx = 0;
            if(substr($line, 0, 1) == ':') {
                $idx = 1;
            }

            Event::dispatch($parts[$idx], [
                'line'      => $line,
                'parts'     => $parts,
                'server'    => substr($parts[0], 1)
            ]);
        }
    }

    public function write($data) {
        if(!feof($this->_socket)) {
            fwrite($this->_socket, $data . PHP_EOL);
            Log::write('irc', "\e[1;33m " . $this->_host . ':' . $this->_port . " \e[0;31m-> \e[0m" . $data);
        }
        else {
            Log::write(Log::DEBUG, 'Connection to server lost unexpectedly.');
            exit;
        }
    }

    public function join($channel, $password = null) {
        Log::write(Log::DEBUG, 'Joining channel ' . $channel);
        $this->write('JOIN ' . $channel . ' ' . $password);
    }

}