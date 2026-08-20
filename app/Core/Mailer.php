<?php
// app/Chore/Mailer.php

class Mailer
{
    private string $from = "noreply@camagru.fr";

    public function send(string $to, string $subject, string $body): bool
    {
        $headers = implode("\r\n", [
            "From: {$this->from}",
            "Content-Type: text/html; charset=UTF-8",
            "MIME-Version: 1.0"
        ]);

        return mail($to, $subject, $body, $headers);
    }

    private function baseUrl(): string {
        $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
        return "{$scheme}://{$host}";
    }

    public function sendVerification(string $to, string $token): bool {
        $link = "{$this->baseUrl()}/verify?token={$token}";

        $body = "
            <h2>Bienvenue sur Camagru !</h2>
            <p>Cliquez sur ce lien pour valider votre inscription :</p>
            <a href='{$link}'>{$link}</a>
        ";

        return $this->send($to, "Validez votre inscription", $body);
    }

    public function sendPasswordReset(string $to, string $token): bool {
        $link = "{$this->baseUrl()}/reset-password?token={$token}";

        $body = "
            <h2>Réinitialisation du mot de passe</h2>
            <p>Cliquez sur ce lien pour choisir un nouveau mot de passe</p>
            <a href='{$link}'>{$link}</a>
        ";

        return $this->send($to, "Réinitialisez votre mot de passe", $body);
    }

    public function sendCommentNotification(string $to, string $commenterPseudo, string $commentContent, string $postImagePath, int $postId): bool {
        $link = "{$this->baseUrl()}/gallery?post={$postId}";
        $imageUrl = "{$this->baseUrl()}{$postImagePath}";
        $pseudo = htmlspecialchars($commenterPseudo);
        $content = htmlspecialchars($commentContent);

        $body = "
            <h2>Nouveau commentaire sur votre post ! 💯</h2>
            <img src='{$imageUrl}' alt='Your post' style='max-width: 300px; border-radius: 8px; display: block; margin-bottom: 1rem;'>
            <p><strong>{$pseudo}</strong> a commenté votre post :</p>
            <p style='font-style: italic;'>\"{$content}\"</p>
            <a href='{$link}'>Répondez lui sur Camagru</a>
        ";

        return $this->send($to, "Nouveau commentaire sur votre post", $body);
    }
}