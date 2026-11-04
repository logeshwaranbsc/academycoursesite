<?php

namespace App\Repositories;

use PDO;

class SettingsRepository extends BaseRepository
{
    protected string $table = 'site_settings';

    public function get(): array
    {
        $stmt = $this->db()->query("SELECT * FROM {$this->table} WHERE id = 1 LIMIT 1");
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$settings) {
            return [
                'id' => 1,
                'academy_name' => 'Meridian Academy',
                'logo_url' => null,
                'email' => 'info@meridianacademy.com',
                'phone' => '+91 98765 43210',
                'whatsapp' => '+91 98765 43210',
                'address' => '123, Education Hub, Koramangala, Bangalore – 560 095',
                'facebook_url' => null,
                'instagram_url' => null,
                'linkedin_url' => null,
                'youtube_url' => null,
            ];
        }

        return $settings;
    }

    public function update(array $data): array
    {
        // Ensure record 1 exists
        $current = $this->get();

        $fields = [];
        $params = [':id' => 1];

        $allowed = [
            'academy_name', 'logo_url', 'email', 'phone', 'whatsapp', 'address',
            'facebook_url', 'instagram_url', 'linkedin_url', 'youtube_url'
        ];

        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $fields[] = "{$f} = :{$f}";
                $params[":{$f}"] = $data[$f];
            }
        }

        if (empty($fields)) {
            return $current;
        }

        $fields[] = "updated_at = NOW()";
        $setClause = implode(', ', $fields);

        // Check if row 1 exists in DB
        $check = $this->db()->query("SELECT id FROM {$this->table} WHERE id = 1")->fetch();
        if ($check) {
            $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id RETURNING *";
            $stmt = $this->db()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Insert initial row
            $sql = "INSERT INTO {$this->table} (id, academy_name, email, phone, whatsapp, address, created_at, updated_at)
                    VALUES (1, 'Meridian Academy', :email, :phone, :whatsapp, :address, NOW(), NOW())
                    RETURNING *";
            $stmt = $this->db()->prepare($sql);
            $stmt->execute([
                ':email' => $data['email'] ?? 'info@meridianacademy.com',
                ':phone' => $data['phone'] ?? null,
                ':whatsapp' => $data['whatsapp'] ?? null,
                ':address' => $data['address'] ?? null,
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
