<?php
// app/Models/Posts.php

require_once __DIR__ . '/../Core/Database.php';

class Post
{
    private PDO $db;
    private int $perPage = 12;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getPaginated($page): array {
        $offset = ($page - 1) * $this->perPage;

        $stmt = $this->db->prepare("
            SELECT posts.*, users.pseudo FROM posts
            JOIN users ON posts.user_id = users.id
            ORDER BY posts.uploaded_at DESC
            LIMIT :perPage OFFSET :offset
        ");
        $stmt->bindValue(':perPage', $this->perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM posts");
        return (int) $stmt->fetchColumn();
    }

    public function getTotalPages(): int {
        return (int) ceil($this->countAll() / $this->perPage);
    }

    public function save(int $userId, string $filename, string $path, ?string $description): bool {
        $stmt = $this->db->prepare("
            INSERT INTO posts (user_id, filename, path, description)
            VALUES (:user_id, :filename, :path, :description)
        ");
        return $stmt->execute([
            ":user_id" => $userId,
            ":filename" => $filename,
            ":path" => $path,
            ":description" => $description
        ]);
    }

    public function getByUserId(int $userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM posts
            WHERE user_id = :user_id
            ORDER BY uploaded_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT posts.*, users.pseudo FROM posts
            JOIN users ON posts.user_id = users.id
            WHERE posts.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch();
        return $post ?: null;
    }

    public function getLikeCount(int $postId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM post_likes
            WHERE post_id = :postId
        ");
        $stmt->execute([':postId' => $postId]);
        return (int) $stmt->fetchColumn();
    }
}