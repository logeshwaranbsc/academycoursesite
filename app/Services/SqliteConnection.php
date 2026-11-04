<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use PDOStatement;
use ReturnTypeWillChange;

class SqliteConnection extends PDO
{
    public function __construct(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null)
    {
        parent::__construct($dsn, $username, $password, $options);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, [SqliteStatement::class, [$this]]);

        // PHP 8.5+ introduces Pdo\Sqlite::createFunction() and deprecates PDO::sqliteCreateFunction()
        if (method_exists($this, 'createFunction')) {
            $this->createFunction('NOW', fn() => date('Y-m-d H:i:s'));
        } elseif (method_exists($this, 'sqliteCreateFunction')) {
            @$this->sqliteCreateFunction('NOW', fn() => date('Y-m-d H:i:s'));
        }
    }

    #[ReturnTypeWillChange]
    public function prepare(string $query, array $options = []): PDOStatement
    {
        $returningTable = null;
        $returningType = null;

        if (preg_match('/\bRETURNING\s+\*/i', $query)) {
            if (preg_match('/^\s*INSERT\s+INTO\s+([a-zA-Z0-9_]+)/i', $query, $m)) {
                $returningType = 'INSERT';
                $returningTable = $m[1];
            } elseif (preg_match('/^\s*UPDATE\s+([a-zA-Z0-9_]+)/i', $query, $m)) {
                $returningType = 'UPDATE';
                $returningTable = $m[1];
            }
            $query = preg_replace('/\s+RETURNING\s+\*/i', '', $query);
        }

        $query = preg_replace('/\bILIKE\b/i', 'LIKE', $query);

        /** @var SqliteStatement $stmt */
        $stmt = parent::prepare($query, $options);
        $stmt->returningTable = $returningTable;
        $stmt->returningType = $returningType;
        return $stmt;
    }

    #[ReturnTypeWillChange]
    public function query(string $query, ?int $fetchMode = null, ...$fetch_mode_args)
    {
        $query = preg_replace('/\bILIKE\b/i', 'LIKE', $query);
        return parent::query($query, ...($fetchMode !== null ? [$fetchMode, ...$fetch_mode_args] : []));
    }

    #[ReturnTypeWillChange]
    public function exec(string $statement): int|false
    {
        $statement = preg_replace('/\bILIKE\b/i', 'LIKE', $statement);
        return parent::exec($statement);
    }
}
