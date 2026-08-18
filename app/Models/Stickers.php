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
            ORDER BY (user_id IS NULL) DESC, created_at ASC
        ");
        $stmt->execute([':user_id' => $userId]);
        $stickers = $stmt->fetchAll();

        $seenHashes = [];
        $deduped = [];
        foreach ($stickers as $sticker) {
            if (isset($seenHashes[$sticker['hash']]))
                continue ;
            $seenHashes[$sticker['hash']] = true;
            $deduped[] = $sticker;
        }
        return $deduped;
    }

    public function save(string $hash, ?int $userId, string $filename, string $path): int|false
    {
        $stmt = $this->db->prepare("
            INSERT INTO stickers (hash, user_id, filename, path)
            VALUES (:hash, :user_id, :filename, :path)
        ");
        $success = $stmt->execute([
            ':hash'     => $hash,
            ':user_id'  => $userId,
            ':filename' => $filename,
            ':path'     => $path
        ]);
        return $success ? (int) $this->db->lastInsertId() : false;
    }

    public function findByHash(string $hash): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM stickers
            WHERE hash = :hash
        ");
        $stmt->execute([':hash' => $hash]);
        return $stmt->fetchAll();
    }

    public function findByPath(string $path): array {
        $stmt = $this->db->prepare("
            SELECT * FROM stickers
            WHERE path = :path"
        );
        $stmt->execute([':path' => $path]);
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
        $sticker = $stmt->fetch();
        return $sticker ?: null;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("
            DELETE FROM stickers
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }
}