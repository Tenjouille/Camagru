<?php
// app/Models/Users.php

require_once __DIR__ . '/../Core/Database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(string $pseudo, string $email, string $password): bool {
        $stmt = $this->db->prepare("
            INSERT INTO users (pseudo, email, password)
            VALUES (:pseudo, :email, :password)
        ");
        return $stmt->execute([
            ':pseudo'   => $pseudo,
            ':email'    => $email,
            ':password' => password_hash($password, PASSWORD_BCRYPT),
        ]);
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = 'comgrade' AND email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function findByPseudo(string $pseudo): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE pseudo = :pseudo LIMIT 1");
        $stmt->execute([':pseudo' => $pseudo]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function setVerificationToken(string $email, string $token): bool {
        $stmt = $this->db->prepare("UPDATE users SET verification_token = :token WHERE email = :email");
        return $stmt->execute([':token' => $token, ':email' => $email]);
    }

    public function verifyAccount(string $token): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET is_verified = 1, verification_token = NULL
            WHERE verification_token = :token
        ");
        $stmt->execute([':token' => $token]);

        // if rowCount == 0, the followed link is obsolete
        return $stmt->rowCount() > 0;
    }

    public function setResetToken(string $email, string $token): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET reset_token = :token, reset_token_expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR)
            WHERE email = :email
        ");
        return $stmt->execute([':token' => $token, ':email' => $email]);
    }

    public function findByResetToken(string $token): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM users
            WHERE reset_token = :token
            AND reset_token_expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function updatePassword(int $id, string $password): bool {
        $stmt = $this->db->prepare("
            UPDATE users SET password = :password, reset_token = NULL, reset_token_expires_at = NULL
            WHERE id = :id
        ");
        return $stmt->execute([
            ':password' => password_hash($password, PASSWORD_BCRYPT),
            ':id' => $id
        ]);
    }

        public function updatePseudo(int $id, string $pseudo): bool {
        $stmt = $this->db->prepare("
            UPDATE users SET pseudo = :pseudo, reset_token = NULL, reset_token_expires_at = NULL
            WHERE id = :id
        ");
        return $stmt->execute([
            ':pseudo' => $pseudo,
            ':id' => $id
        ]);
    }


    public function toggleEmailNotification(int $id, int $is_enabled): bool {
        $stmt = $this->db->prepare("
            UPDATE users SET email_notification = :is_enabled
            WHERE id = :id
        ");
        return $stmt->execute([
            ':is_enabled' => $is_enabled,
            ':id' => $id
        ]);
    }
}