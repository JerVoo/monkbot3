<?php
namespace Monkbot\Framework;

/**
 * Registry management class
 * @package Monkbot\Registry
 */
class Registry {
    /**
     * Registry data holder
     * @var array
     */
    private static $_data = [];

    /**
     * @var Registry
     */
    private static $_instance;

    /**
     * Get a singleton instance
     * @return Registry
     */
    public static function getInstance() {
        if(!self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

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
    public function setData(string $key, $value) {
        self::$_data[$key] = $value;
        Log::write(Log::DEBUG, 'Written value for ' . $key . ' into registry');
        return $this;
    }

    /**
     * Get data from the registry
     * @param string $key
     * @return null
     */
    public function getData($key = null) {
        Log::write(Log::DEBUG, 'Fetching value for ' . $key . ' from registry');

        if($key) {
            return self::$_data[$key] ?? null;
        }

        return self::$_data;
    }

    /**
     * Unset registry data
     * @param string $key
     * @return $this
     */
    public function unsData(string $key) {
        Log::write(Log::DEBUG, 'Unsetting ' . $key . ' from registry');
        if(isset(self::$_data[$key])) {
            unset(self::$_data[$key]);

            Log::write(Log::DEBUG, 'Succesfully remove ' . $key . ' from registry');
        }

        return $this;
    }

    public function fromCamelCase($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}