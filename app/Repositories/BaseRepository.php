<?php

namespace App\Repositories;

use App\Services\DatabaseService;
use PDO;

abstract class BaseRepository
{
    protected string $table;
    protected string $primaryKey = 'id';

    protected function db(): PDO
    {
        return DatabaseService::getConnection();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function all(string $orderBy = 'id', string $direction = 'ASC'): array
    {
        $stmt = $this->db()->query("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db()->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function toggleStatus(int $id, string $statusColumn = 'status'): ?string
    {
        $row = $this->find($id);
        if (!$row) {
            return null;
        }

        $newStatus = ($row[$statusColumn] === 'active') ? 'inactive' : 'active';
        $stmt = $this->db()->prepare("UPDATE {$this->table} SET {$statusColumn} = :status, updated_at = NOW() WHERE {$this->primaryKey} = :id");
        $stmt->execute([':status' => $newStatus, ':id' => $id]);
        return $newStatus;
    }
}
