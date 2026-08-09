<?php
// app/Models/Stickers.php

require_once __DIR__ . '/../Core/Database.php';

class Sticker {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAvailable(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM stickers
            WHERE user_id IS NULL OR user_id = :user_id
            ORDER BY created_at ASC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function save(int $userId, string $filename, string $path): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO stickers (user_id, filename, path)
            VALUES (:user_id, :filename, :path)
        ");
        return $stmt->execute([
            ':user_id'  => $userId,
            ':filename' => $filename,
            ':path'     => $path
        ]);
    }
}