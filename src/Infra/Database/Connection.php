<?php

namespace App\Infra\Database;

use PDO;

class Connection
{
    private static ?PDO $instance = null;

    public static function get(): PDO
    {
        if (self::$instance === null) {
            // Caminho absoluto para funcionar no XAMPP
            $dbPath = __DIR__ . '/../../../database.sqlite';
            
            self::$instance = new PDO('sqlite:' . $dbPath);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            self::$instance->exec("
                CREATE TABLE IF NOT EXISTS tickets (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    plate TEXT NOT NULL,
                    vehicle_type TEXT NOT NULL,
                    entry_time TEXT NOT NULL,
                    exit_time TEXT
                )
            ");
        }

        return self::$instance;
    }
}