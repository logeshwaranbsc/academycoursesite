<?php

namespace App\Repositories;

use PDO;

class GalleryRepository extends BaseRepository
{
    protected string $table = 'gallery';

    public function getActive(?string $category = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active'";
        $params = [];

        if (!empty($category) && $category !== 'All') {
            $sql .= " AND category = :category";
            $params[':category'] = $category;
        }

        $sql .= " ORDER BY display_order ASC, created_at DESC";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories(): array
    {
        $stmt = $this->db()->query("SELECT DISTINCT category FROM {$this->table} WHERE category IS NOT NULL AND category != '' AND status = 'active' ORDER BY category ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function paginate(int $page = 1, int $perPage = 15, ?string $search = null, ?string $category = null): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "title ILIKE :search";
            $params[':search'] = "%{$search}%";
        }

        if (!empty($category) && $category !== 'All') {
            $where[] = "category = :category";
            $params[':category'] = $category;
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
        $sql = "INSERT INTO {$this->table} (title, image_url, category, status, display_order, created_at, updated_at)
                VALUES (:title, :image_url, :category, :status, :display_order, NOW(), NOW())
                RETURNING *";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':image_url' => $data['image_url'],
            ':category' => $data['category'] ?? null,
            ':status' => $data['status'] ?? 'active',
            ':display_order' => (int)($data['display_order'] ?? 0),
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): ?array
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['title', 'image_url', 'category', 'status', 'display_order'] as $field) {
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
