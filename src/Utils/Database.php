<?php

namespace App\Utils;

class Database
{
    private static ?\PDO $instance = null;

    /**
     * The constructor is private to prevent direct creation of object.
     */
    private function __construct() {}

    /**
     * Gets the single instance of the PDO connection.
     */
    public static function getInstance(): ?\PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $name = $_ENV['DB_NAME'] ?? 'bookpy';
            $user = $_ENV['DB_USER'] ?? 'bookpy';
            $pass = $_ENV['DB_PASS'] ?? '1234';

            try {
                self::$instance = new \PDO(
                    "mysql:host={$host};dbname={$name};charset=utf8mb4",
                    $user,
                    $pass,
                    [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
                );
                error_log("DB Singleton connected: host={$host} db={$name} user={$user}");
            } catch (\PDOException $e) {
                error_log("DB Singleton connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}