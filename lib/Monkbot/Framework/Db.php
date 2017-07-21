<?php
namespace Monkbot\Framework;
use LessQL\Database;

/**
 * Database manage class
 * @package Monkbot\Framework
 */
final class Db extends Database {
    /**
     * @var Db
     */
    private static $_instance;

    private static $_dbfile;
    private $_db;

    protected $pdo;

    /**
     * Database class constructor - initializes db and loads it into memory
     * @throws \Exception
     */
    public function __construct($name = 'monkbot')
    {
        if(defined('DBDIR')) { return; }

        Log::write(Log::DEBUG, 'Initializing database');

        define('DBDIR', ROOT . DIRECTORY_SEPARATOR . 'var/db');
        self::$_dbfile = DBDIR . DIRECTORY_SEPARATOR . $name . '.db';

        Log::write(Log::DEBUG, 'Checking if directory exists');

        if(!is_dir(DBDIR)) {
            Log::write(Log::DEBUG, 'Directory does not exists, attempting to create it');
            @mkdir(DBDIR, 0777, true);

            if(!is_dir(DBDIR)) {
                throw new \Exception('Directory could not be created');
            }

            Log::write(Log::DEBUG, 'Directory has been created successfully');
        }

        $this->pdo = new \PDO('sqlite:' . self::$_dbfile);
        $this->_db = new Database($this->pdo);
    }

    /**
     * Get a database instance
     * @return Db
     */
    public static function getInstance($name = null) {
        if(!self::$_instance) {
            self::$_instance = new self($name);
        }

        return self::$_instance;
    }

    public function getDb() {
        return $this->_db;
    }
}