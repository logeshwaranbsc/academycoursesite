<?php

namespace App\Repositories;

use PDO;

class CultureVideoRepository extends BaseRepository
{
    protected string $table = 'culture_videos';

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
            $where[] = "(title ILIKE :search OR description ILIKE :search)";
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
        $sql = "INSERT INTO {$this->table} (title, description, video_url, thumbnail_url, status, display_order, created_at, updated_at)
                VALUES (:title, :description, :video_url, :thumbnail_url, :status, :display_order, NOW(), NOW())
                RETURNING *";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':video_url' => $data['video_url'],
            ':thumbnail_url' => $data['thumbnail_url'] ?? null,
            ':status' => $data['status'] ?? 'active',
            ':display_order' => (int)($data['display_order'] ?? 0),
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): ?array
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['title', 'description', 'video_url', 'thumbnail_url', 'status', 'display_order'] as $field) {
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
