<?php

namespace App\Repositories;

use PDO;

class PlacementRepository extends BaseRepository
{
    protected string $table = 'placed_students';

    public function getActive(): array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY display_order ASC, created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function paginate(int $page = 1, int $perPage = 15, ?string $search = null): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(student_name ILIKE :search OR company ILIKE :search OR course ILIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db()->prepare("SELECT COUNT(*) FROM {$this->table} {$whereClause}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY display_order ASC, created_at DESC LIMIT :limit OFFSET :offset";
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
        $sql = "INSERT INTO {$this->table} (student_name, course, company, designation, image_url, description, status, display_order, created_at, updated_at)
                VALUES (:student_name, :course, :company, :designation, :image_url, :description, :status, :display_order, NOW(), NOW())
                RETURNING *";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':student_name' => $data['student_name'],
            ':course' => $data['course'],
            ':company' => $data['company'],
            ':designation' => $data['designation'],
            ':image_url' => $data['image_url'] ?? null,
            ':description' => $data['description'] ?? null,
            ':status' => $data['status'] ?? 'active',
            ':display_order' => (int)($data['display_order'] ?? 0),
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): ?array
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['student_name', 'course', 'company', 'designation', 'image_url', 'description', 'status', 'display_order'] as $field) {
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
