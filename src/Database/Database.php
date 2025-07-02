<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;
    
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/database.php';
            
            try {
                $dsn = "sqlite:" . $config['database'];
                self::$connection = new PDO($dsn, null, null, $config['options']);
            } catch (PDOException $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
    
    public static function closeConnection(): void
    {
        self::$connection = null;
    }
} 