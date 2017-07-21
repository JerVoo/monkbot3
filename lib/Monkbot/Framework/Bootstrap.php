<?php
namespace Monkbot\Framework;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Log.php';

use Monkbot\Framework\Db\Table;
use Monkbot\Framework\Registry;

final class Bootstrap {
    /**
     * Initiate the bot
     * @return Starter
     */
    public function init() : Starter {
        Log::write(Log::DEBUG, 'Bootstrapper being initiated');
        Log::write(Log::DEBUG, 'Registering autoloader');

        // Register class autoloader
        spl_autoload_register([
            $this,
            'autoload'
        ], true, true);

        // Define paths
        Log::write(Log::DEBUG, 'Defining path into definitions');
        define('ROOT', realpath(dirname(__FILE__) . '/../../../'));
        define('LIB_ROOT', ROOT . '/lib');
        define('APP_ROOT', ROOT  . '/app');
        define('CODE_ROOT', APP_ROOT . '/code');
        define('ETC_ROOT', APP_ROOT . '/etc');

        Log::write(Log::DEBUG, 'Writing paths into registry');
        $registry = Registry::getInstance();
        $registry->setData('root', ROOT);
        $registry->setData('libroot', LIB_ROOT);
        $registry->setData('approot', APP_ROOT);
        $registry->setData('coderoot', CODE_ROOT);
        $registry->setData('etcroot', ETC_ROOT);

        $this->initConfig();

        return new Starter();
    }

    /**
     * Autoload a class by it's class name. Should only be called by the class autoloader.
     * @param string $className
     * @return null
     */
    public function autoload(string $className) : void {
        Log::write(Log::DEBUG, 'Autoloading class ' . $className);

        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

        Log::write(Log::DEBUG, 'Filename resolved to ' . $classPath);

        $availableDirs = [
            LIB_ROOT,
            CODE_ROOT
        ];

        foreach($availableDirs as $dir) {
            Log::write(Log::DEBUG, 'Testing directory ' . $dir . ', filename ' . $dir . DIRECTORY_SEPARATOR . $classPath);

            if(file_exists($dir . DIRECTORY_SEPARATOR . $classPath)) {
                Log::write(Log::DEBUG, 'Found file, testing if class exists');
                require_once $dir . DIRECTORY_SEPARATOR . $classPath;

                if(!class_exists($className)) {
                    Log::write(Log::DEBUG, 'Class could not be found in file ' . $dir . DIRECTORY_SEPARATOR . $classPath);
                    throw new \Exception('Could not load class "' . $className . '": file was found, but class was not.');
                }
                else {
                    Log::write(Log::DEBUG, 'Class has been found in file ' . $dir . DIRECTORY_SEPARATOR . $classPath);
                    break;
                }
            }
        }
    }

    /**
     * Initialize configuration
     * @return void
     */
    private function initConfig() : void {
        Config::getInstance();
    }
}