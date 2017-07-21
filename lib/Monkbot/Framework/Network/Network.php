<?php
namespace Monkbot\Framework\Network;

use Monkbot\Framework\Config\Item;
use Monkbot\Framework\Log;
use Monkbot\Framework\Db;

class Network {
    /**
     * @var Network
     */
    private static $_instance;

    /**
     * @var Item
     */
    private $_networkConfig;

    /**
     *  @var string
     */
    private $_name;

    public function __construct(string $name, Item $networkConfig)
    {
        self::$_instance = $this;
        $this->_networkConfig = $networkConfig;
        $this->_name = $name;

        $this->initDb();
        $this->initModules();
    }

    /**
     * Return class instance
     * @return Network
     */
    public function getInstance() {
        return self::$_instance;
    }

    public function connect() {
        $servers = $this->_networkConfig->get('servers');
        $keys    = $servers->getKeys();

        foreach($keys as $key) {
            Log::write(Log::DEBUG, 'Connecting to server #' . $key);

            $password = null;
            $port = null;
            $host = null;

            $serverData = (string) $servers->get($key);
            $info = explode(' ', $serverData);
            $data = explode(':', $info[0]);

            $password = $info[1] ?? null;
            $host = $data[0];
            $port = $data[1] ?? 6667;

            Log::write(Log::DEBUG, 'Connecting to ' . $host . ':' . $port);
            $server = new Server($this->_networkConfig, $host, $port, $password);
            $server->connect();
        }

        //$this->connect();
    }

    /**
     * Database initializer
     * @return void
     */
    private function initDb() : void {
        Log::write(Log::DEBUG, 'Initializing database');

        Db::getInstance($this->_name);
    }

    private function initModules() {
        Log::write(Log::DEBUG, 'Initializing modules');
        $directoryIterator = new \DirectoryIterator(CODE_ROOT);

        Log::write(Log::DEBUG, 'Looking for modules in ' . CODE_ROOT);

        foreach($directoryIterator as $directory) {
            if($directory->isDir() && !$directory->isDot()) {
                Log::write(Log::DEBUG, 'Checking for modules in ' . $directory->getRealPath());

                $iterator = new \DirectoryIterator($directory->getRealPath());
                foreach($iterator as $dir) {
                    if($dir->isDir() && !$dir->isDot()) {
                        $path = $dir->getRealPath();
                        if(file_exists($path . DIRECTORY_SEPARATOR . 'registration.php')) {
                            require_once $path . DIRECTORY_SEPARATOR . 'registration.php';
                        }
                    }
                }
            }
        }
    }
}