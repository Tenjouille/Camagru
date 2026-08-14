<?php
// app/Core/Request.php

// Wrapper statique autour de $_GET / $_POST / $_SERVER / $_FILES.
class Request {
    private function __construct() {}

    public static function method(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public static function uri(): string {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    public static function isAjax(): bool {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    public static function post(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    public static function get(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    // Présence d'un champ POST (ex: case à cocher non envoyée si décochée).
    public static function has(string $key): bool {
        return isset($_POST[$key]);
    }

    // Retourne l'entrée $_FILES['key'] (array avec name/tmp_name/error/...),
    // ou null si aucun fichier n'a été envoyé sous ce nom.
    public static function file(string $key): ?array {
        return $_FILES[$key] ?? null;
    }
}
