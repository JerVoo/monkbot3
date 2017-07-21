<?php
namespace Monkbot\Core\Event;

use Monkbot\Framework\Event\AbstractEvent;
use Monkbot\Framework\Log;
use Monkbot\Framework\Network\Server;

class PingChecker extends AbstractEvent {
    private static $_pingCookie   = '';
    private static $_lastCheck    = 0;
    private static $_lastResponse = 0;
    private static $_fails = 0;

    public function check() {
        if(self::$_lastResponse == 0) {
            self::$_lastResponse = time();
            self::$_lastCheck    = time();

            $this->startCheck();
        }
    }

    public function startCheck() {
        $timeout = rand(10, 30);
        $cookie = $this->getCookie();
        self::$_pingCookie = $cookie;
        self::$_lastCheck  = time() + $timeout;
        self::$_lastResponse = 0;

        $pid = pcntl_fork();

        if(!$pid) {
            sleep($timeout + 1);
            Server::getInstance()->write('PING :' . $cookie);

            exit;
        }
    }

    public function checkCookie() {
        $incomingCookie = substr($this->getParts()[3], 1);
        $cookie = self::$_pingCookie;
        $lastCheck = self::$_lastCheck;

        Log::write(Log::DEBUG, 'Received ping cookie ' . $incomingCookie . ' - Need ' . $cookie);
        if(time() < $lastCheck + 10 && $cookie == $incomingCookie) {
            self::$_lastCheck = time();
            self::$_lastResponse = time();
            self::$_fails = 0;
        }
        else {
            self::$_fails++;

            if(self::$_fails > 3) {
                Server::getInstance()->write('QUIT :Something went wrong when checking for ping.');
            }
        }

        $this->startCheck();
    }

    private function getCookie() {
        $max = 60466175;

        return strtoupper(sprintf(
            "%05s-%05s-%05s-%05s-%05s",
            base_convert(mt_rand(0, $max), 10, 36),
            base_convert(mt_rand(0, $max), 10, 36),
            base_convert(mt_rand(0, $max), 10, 36),
            base_convert(mt_rand(0, $max), 10, 36),
            base_convert(mt_rand(0, $max), 10, 36)
        ));
    }
}