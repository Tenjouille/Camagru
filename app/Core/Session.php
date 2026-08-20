<?php
// app/Core/Session.php

// Wrapper statique autour de $_SESSION 
class Session {
    private function __construct() {}

    // À appeler une seule fois, au tout début du bootstrap (public/index.php).
    // La garde évite un warning PHP si start() est appelée plusieurs fois.
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'httponly' => true,   // inaccessible en JS (document.cookie) — limite la casse en cas de XSS
                'samesite' => 'Lax',  // pas envoyé sur une requête cross-site déclenchée par un autre site — complète le CSRF
                // 'secure' => true,  // à activer une fois servi en HTTPS (cassera les cookies en HTTP sinon)
            ]);
            session_start();
        }
    }

    public static function destroy(): void {
        session_destroy();
    }

    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function isAdmin() {
        if (Session::has('user') && Session::get('user')['role'] === 'admin')
            return true;
        return false;
    }

    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
