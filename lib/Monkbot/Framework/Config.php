<?php
namespace Monkbot\Framework;

use Monkbot\Framework\Config\Item;

class Config {
    /**
     * @var Config
     */
    private static $_instance;

    /**
     * @var array
     */
    private $_config = [];

    public function __construct()
    {
        Log::write(Log::DEBUG, 'Initializing configuration class');
        define('CONFIGFILE', ETC_ROOT . DIRECTORY_SEPARATOR . 'config.php');

        Log::write(Log::DEBUG, 'Checking if configuration file exists');
        if(!file_exists(CONFIGFILE)) {
            throw new \Exception('Configuration file could not be found in ' . CONFIGFILE);
        }

        Log::write(Log::DEBUG, 'Configuration file has been found');
        Log::write(Log::DEBUG, 'Loading configuraton into memory');

        $config = include(CONFIGFILE);
        foreach($config as $key => $value) {
            $this->_config[$key] = new Item($value);
        }

        Log::write(Log::DEBUG, 'Configuration loaded into memory');
    }

    /**
     * Create a method instance and store it
     * @return Config
     */
    public static function getInstance() {
        Log::write(Log::DEBUG, 'Fetching method instance for ' . __CLASS__);

        if(!self::$_instance) {
            Log::write(Log::DEBUG, 'Creating new method instance for ' . __CLASS__);
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function get($path) {
        Log::write(Log::DEBUG, 'Getting configuration value by path ' . $path);
        $pathParts = explode('/', $path);

        if(isset($this->_config[$pathParts[0]])) {
            $data = $this->_config[$pathParts[0]];
            array_shift($pathParts);

            if(count($pathParts)) {
                $data = $data->get(implode('/', $pathParts));
            }

            return $data;
        }

        return null;
    }
}