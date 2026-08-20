<?php
// app/Core/Csrf.php

require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Request.php';

class Csrf
{
    public static function isValid(): bool {
        $sessionToken = Session::get('csrf_token');
        $sentToken = Request::post('csrf_token') ?? Request::header('X-CSRF-Token');

        return $sessionToken && $sentToken && hash_equals($sessionToken, $sentToken);
    }

    public static function guard(): void {
        if (self::isValid())
            return;

        if (Request::isAjax()) {
            ob_end_clean();
            http_response_code(419);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Session expirée, merci de recharger la page']);
            exit;
        }

        http_response_code(403);
        echo "403 - Token CSRF invalide";
        exit;
    }
}
