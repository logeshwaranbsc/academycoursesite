<?php

namespace App\Repositories;

use PDO;

class EnquiryRepository extends BaseRepository
{
    protected string $table = 'enquiries';

    public function paginate(int $page = 1, int $perPage = 20, ?string $search = null, ?string $status = null): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(name ILIKE :search OR email ILIKE :search OR phone ILIKE :search OR course ILIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        if (!empty($status)) {
            $where[] = "status = :status";
            $params[':status'] = $status;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db()->prepare("SELECT COUNT(*) FROM {$this->table} {$whereClause}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public function create(array $data): array
    {
        $sql = "INSERT INTO {$this->table} (name, email, phone, course, message, status, created_at, updated_at)
                VALUES (:name, :email, :phone, :course, :message, :status, NOW(), NOW())
                RETURNING *";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'] ?? null,
            ':phone' => $data['phone'],
            ':course' => $data['course'] ?? null,
            ':message' => $data['message'] ?? null,
            ':status' => $data['status'] ?? 'new',
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): ?array
    {
        $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id RETURNING *";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':status' => $status, ':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getRecent(int $limit = 10): array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByStatus(string $status): int
    {
        return $this->count("status = :status", [':status' => $status]);
    }
}
