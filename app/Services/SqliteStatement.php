<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use PDOStatement;
use ReturnTypeWillChange;

class SqliteStatement extends PDOStatement
{
    private PDO $pdo;
    public ?string $returningTable = null;
    public ?string $returningType = null;
    public ?array $simulatedRow = null;
    public array $boundParams = [];

    protected function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    #[ReturnTypeWillChange]
    public function bindValue($param, $value, $type = PDO::PARAM_STR): bool
    {
        $this->boundParams[$param] = $value;
        return parent::bindValue($param, $value, $type);
    }

    #[ReturnTypeWillChange]
    public function bindParam($param, &$var, $type = PDO::PARAM_STR, $maxLength = 0, $driverOptions = null): bool
    {
        $this->boundParams[$param] = &$var;
        return parent::bindParam($param, $var, $type, $maxLength, $driverOptions);
    }

    #[ReturnTypeWillChange]
    public function execute($params = null): bool
    {
        if (is_array($params)) {
            $this->boundParams = array_merge($this->boundParams, $params);
        }

        $res = parent::execute($params);

        if ($res && $this->returningTable !== null) {
            if ($this->returningType === 'INSERT') {
                $lastId = (int)$this->pdo->lastInsertId();
                if ($lastId > 0) {
                    $fetchStmt = $this->pdo->prepare("SELECT * FROM {$this->returningTable} WHERE id = :id LIMIT 1");
                    $fetchStmt->execute([':id' => $lastId]);
                    $this->simulatedRow = $fetchStmt->fetch(PDO::FETCH_ASSOC) ?: [];
                }
            } elseif ($this->returningType === 'UPDATE') {
                $id = $this->boundParams[':id'] ?? $this->boundParams['id'] ?? null;
                if ($id !== null) {
                    $fetchStmt = $this->pdo->prepare("SELECT * FROM {$this->returningTable} WHERE id = :id LIMIT 1");
                    $fetchStmt->execute([':id' => $id]);
                    $this->simulatedRow = $fetchStmt->fetch(PDO::FETCH_ASSOC) ?: [];
                }
            }
        }

        return $res;
    }

    #[ReturnTypeWillChange]
    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed
    {
        if ($this->simulatedRow !== null) {
            $row = $this->simulatedRow;
            $this->simulatedRow = null;
            return $row;
        }
        return parent::fetch($mode, $cursorOrientation, $cursorOffset);
    }
}
