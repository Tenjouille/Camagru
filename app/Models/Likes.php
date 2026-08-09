<?php
// app/Models/Likes.php

require_once __DIR__ . '/../Core/Database.php';

class Like {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function toggle(int $postId, int $userId) {
        if ($this->hasLiked($postId, $userId)) {
            $stmt = $this->db->prepare("
                DELETE FROM post_likes
                WHERE post_id = :postId AND user_id = :userId
            ");
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO post_likes (post_id, user_id)
                VALUES (:postId, :userId)
            ");
        }
         return $stmt->execute([':postId' => $postId, ':userId' => $userId]);
    }

    public function hasLiked(int $postId, int $userId): bool {
        $stmt = $this->db->prepare("
            SELECT EXISTS (
                SELECT 1
                FROM post_likes
                WHERE post_id = :postId AND user_id = :userId
            )
        ");
        $stmt->execute([':postId' => $postId, ':userId' => $userId]);
        return (bool) $stmt->fetchColumn();
    }
}