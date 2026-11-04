<?php

namespace App\Repositories;

use PDO;

class CourseRepository extends BaseRepository
{
    protected string $table = 'courses';

    public function getActive(): array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findActive(int $id): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE id = :id AND status = 'active' LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function paginate(int $page = 1, int $perPage = 15, ?string $search = null): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(title ILIKE :search OR description ILIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Total count
        $countStmt = $this->db()->prepare("SELECT COUNT(*) FROM {$this->table} {$whereClause}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Data query
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
        $sql = "INSERT INTO {$this->table} (title, description, image_url, duration, mode, price, status, created_at, updated_at)
                VALUES (:title, :description, :image_url, :duration, :mode, :price, :status, NOW(), NOW())
                RETURNING *";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':image_url' => $data['image_url'] ?? null,
            ':duration' => $data['duration'],
            ':mode' => $data['mode'] ?? 'Classroom',
            ':price' => $data['price'] ?? null,
            ':status' => $data['status'] ?? 'active',
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): ?array
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['title', 'description', 'image_url', 'duration', 'mode', 'price', 'status'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return $this->find($id);
        }

        $fields[] = "updated_at = NOW()";
        $setClause = implode(', ', $fields);

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id RETURNING *";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
