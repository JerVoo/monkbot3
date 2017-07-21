<?php
namespace Monkbot\Framework;

class Registration {
    private static $_modules = [];
    private static $_events = [];

    public static function registerModule($_module, $path) {
        if(!isset(self::$_modules[$_module])) {
            Log::write(Log::DEBUG, 'Registering module ' . $_module);

            self::$_modules[$_module] = $path;

            if(file_exists($path . DIRECTORY_SEPARATOR . 'contracts.php')) {
                require_once $path . DIRECTORY_SEPARATOR . 'contracts.php';
            }
        }
    }

    public static function registerEvent($event, $callable) {
        if(!isset(self::$_events[$event])) {
            self::$_events[$event] = [];
        }

        Log::write(Log::DEBUG, 'Registering new caller for event ' . $event);
        self::$_events[$event][] = $callable;
    }

    public static function dispatchEvent($event, $args) {
        if(isset(self::$_events[$event])) {
            foreach(self::$_events[$event] as $callable) {
                $object = new $callable[0];
                $method = $callable[1];
                $object->setData($args);
                $object->setEvent($event);
                $object->prepare();
                $object->$method();
            }
        }
    }
}