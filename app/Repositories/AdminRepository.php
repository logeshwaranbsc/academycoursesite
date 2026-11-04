<?php

namespace App\Repositories;

use PDO;

class AdminRepository extends BaseRepository
{
    protected string $table = 'admins';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $stmt = $this->db()->prepare("UPDATE {$this->table} SET password = :password, updated_at = NOW() WHERE id = :id");
        return $stmt->execute([':password' => $hashedPassword, ':id' => $id]);
    }
}
