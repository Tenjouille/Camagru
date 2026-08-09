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

    public function sendVerification(string $to, string $token): bool {
        $link = "http://localhost:8080/verify?token={$token}";

        $body = "
            <h2>Welcome on Camagru !</h2>
            <p>Please, click on this link to validate your registration :</p>
            <a href='{$link}'>{$link}</a>
        ";

        return $this->send($to, "Confirm you registration", $body);
    }

    public function sendPasswordReset(string $to, string $token): bool {
        $link = "http://localhost:8080/reset-password?token={$token}";

        $body = "
            <h2>Password Reinitialization</h2>
            <p>Please, click on this link to choose a new password</p>
            <a href='{$link}'>{$link}</a>
        ";

        return $this->send($to, "Reset your password", $body);
    }
}