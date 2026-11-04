<?php

namespace App\Repositories;

use PDO;

class BatchRepository extends BaseRepository
{
    protected string $table = 'batches';

    public function getUpcomingActive(?int $courseId = null): array
    {
        $sql = "SELECT b.*, c.title AS course_title
                FROM {$this->table} b
                JOIN courses c ON c.id = b.course_id
                WHERE b.status = 'active' AND b.start_date >= CURRENT_DATE";
        $params = [];

        if ($courseId !== null) {
            $sql .= " AND b.course_id = :course_id";
            $params[':course_id'] = $courseId;
        }

        $sql .= " ORDER BY b.start_date ASC";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Normalize course object for view compatibility
        return array_map(function ($r) {
            $r['course'] = ['id' => $r['course_id'], 'title' => $r['course_title']];
            return $r;
        }, $rows);
    }

    public function paginate(int $page = 1, int $perPage = 15, ?string $search = null): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(b.batch_name ILIKE :search OR c.title ILIKE :search OR b.location ILIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db()->prepare("SELECT COUNT(*) FROM {$this->table} b JOIN courses c ON c.id = b.course_id {$whereClause}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT b.*, c.title AS course_title
                FROM {$this->table} b
                JOIN courses c ON c.id = b.course_id
                {$whereClause}
                ORDER BY b.start_date ASC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $items = array_map(function ($r) {
            $r['course'] = ['id' => $r['course_id'], 'title' => $r['course_title']];
            return $r;
        }, $items);

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
        $sql = "INSERT INTO {$this->table} (course_id, batch_name, start_date, start_time, duration, location, available_seats, status, created_at, updated_at)
                VALUES (:course_id, :batch_name, :start_date, :start_time, :duration, :location, :available_seats, :status, NOW(), NOW())
                RETURNING *";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':course_id' => $data['course_id'],
            ':batch_name' => $data['batch_name'],
            ':start_date' => $data['start_date'],
            ':start_time' => $data['start_time'],
            ':duration' => $data['duration'],
            ':location' => $data['location'],
            ':available_seats' => $data['available_seats'] ?? 30,
            ':status' => $data['status'] ?? 'active',
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): ?array
    {
        $fields = [];
        $params = [':id' => $id];

        foreach (['course_id', 'batch_name', 'start_date', 'start_time', 'duration', 'location', 'available_seats', 'status'] as $field) {
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
