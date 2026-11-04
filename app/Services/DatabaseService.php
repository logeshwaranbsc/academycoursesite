<?php

namespace App\Services;

use App\Core\Config;
use PDO;
use PDOException;
use RuntimeException;

class DatabaseService
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $defaultDriver = Config::get('database.default', 'pgsql');
        $config = Config::get("database.connections.{$defaultDriver}");

        if (!$config) {
            throw new RuntimeException("Database configuration for [{$defaultDriver}] not found.");
        }

        try {
            if ($defaultDriver === 'pgsql') {
                $host = $config['host'];
                $port = $config['port'];
                $database = $config['database'];
                $sslmode = $config['sslmode'] ?? 'prefer';

                $dsn = "pgsql:host={$host};port={$port};dbname={$database};sslmode={$sslmode}";
                self::$pdo = new PDO($dsn, $config['username'], $config['password'], $config['options'] ?? [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } elseif ($defaultDriver === 'sqlite') {
                $dbPath = $config['database'];
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $dsn = "sqlite:{$dbPath}";
                self::$pdo = new SqliteConnection($dsn, null, null, $config['options'] ?? [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                self::$pdo->exec('PRAGMA foreign_keys = ON;');

                // Auto-create schema and seeds if not already created
                $tableCheck = self::$pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='admins'")->fetch();
                if (!$tableCheck) {
                    $schemaFile = dirname(dirname(__DIR__)) . '/database/schema_sqlite.sql';
                    if (file_exists($schemaFile)) {
                        self::$pdo->exec(file_get_contents($schemaFile));
                    }
                }
            } else {
                throw new RuntimeException("Unsupported database driver: {$defaultDriver}");
            }

            return self::$pdo;
        } catch (PDOException $e) {
            // Log error without exposing credentials in production
            error_log("Database connection failed: " . $e->getMessage());
            throw new RuntimeException("Database connection error. Please verify database credentials.");
        }
    }

    public static function setConnection(?PDO $pdo): void
    {
        self::$pdo = $pdo;
    }
}
