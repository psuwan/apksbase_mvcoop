<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    /** @var PDO|null */
    private static ?PDO $pdo = null;

    /**
    * Initialize PDO singleton from configuration array.
    * Config keys: host, port, database, username, password, charset, options
    * @param array $config
    * @return void
    */
    public static function init(array $config)
    {
        if (self::$pdo !== null) { return; }
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $db   = $config['database'] ?? '';
        $user = $config['username'] ?? '';
        $pass = $config['password'] ?? '';
        $charset = $config['charset'] ?? 'utf8mb4';
        $options = $config['options'] ?? array();

        $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $db . ';charset=' . $charset;

        $defaultOptions = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        );
        $options = $options + $defaultOptions;

        try {
            self::$pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Do not expose credentials; keep message generic
            throw new \RuntimeException('Database connection failed. ' . $e->getMessage());
        }
    }

    /**
    * Get PDO instance (call init() first).
    * @return PDO
    */
    public static function pdo(): ?PDO
    {
        if (self::$pdo === null) {
            throw new \RuntimeException('Database is not initialized. Call Database::init() with configuration.');
        }
        return self::$pdo;
    }
}
