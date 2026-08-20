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

    public static function header(string $name): ?string {
        $key = 'HTTP_' . str_replace('-', '_', strtoupper($name));
        return $_SERVER[$key] ?? null;
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

    public static function has(string $key): bool {
        return isset($_POST[$key]);
    }

    public static function file(string $key): ?array {
        return $_FILES[$key] ?? null;
    }
}
