<?php
// app/Models/Comments.php

require_once __DIR__ . '/../Core/Database.php';

class Comment {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByPostId(int $postId): array{
        $stmt = $this->db->prepare("
            SELECT post_comments.*, users.pseudo
            FROM post_comments
            JOIN users ON post_comments.user_id = users.id
            WHERE post_id = :postId
            ORDER BY created_at DESC
        ");
        $stmt->execute([":postId" => $postId]);
        return $stmt->fetchAll();
    }

    public function create(int $postId, int $userId, string $content) {
        $stmt = $this->db->prepare("
            INSERT INTO post_comments (post_id, user_id, content)
            VALUES (:post_id, :user_id, :content)
        ");
        return $stmt->execute([
            ':post_id' => $postId,
            ':user_id' => $userId,
            ':content' => $content
        ]);
    }
}