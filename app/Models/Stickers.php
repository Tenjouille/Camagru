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

    public function save(string $hash, ?int $userId, string $filename, string $path): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO stickers (hash, user_id, filename, path)
            VALUES (:hash, :user_id, :filename, :path)
        ");
        return $stmt->execute([
            ':hash'     => $hash,
            ':user_id'  => $userId,
            ':filename' => $filename,
            ':path'     => $path
        ]);
    }

    public function findByHash(string $hash): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM stickers
            WHERE hash = :hash
        ");
        $stmt->execute([':hash' => $hash]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM stickers
            WHERE id = :id
        ");
        $stmt->execute([
            ':id' => $id,
        ]);
        return $stmt->fetchAll();
    }

    public function delete(int $id) {
        $stmt = $this->db->prepare("
            DELETE FROM stickers
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }
}