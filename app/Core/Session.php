<?php
// app/Core/Session.php

// Wrapper statique autour de $_SESSION : centralise les accès pour ne plus
// avoir à relire/réécrire $_SESSION directement dans les contrôleurs/vues.
class Session {
    private function __construct() {}

    // À appeler une seule fois, au tout début du bootstrap (public/index.php).
    // La garde évite un warning PHP si start() est appelée plusieurs fois.
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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

    // Détruit toute la session (logout).
    public static function destroy(): void {
        session_destroy();
    }

    public static function isAdmin() {
        if (Session::has('user') && Session::get('user')['role'] === 'admin')
            return true;
        return false;
    }
}
