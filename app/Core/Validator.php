<?php
// app/Core/Validator.php

class Validator
{
    private function __construct() {}

    public static function password($password, $default = '') {
        $errors = [];
        $password ??= $default;
        if (strlen($password) < 8)
            $errors[] = "Password must be at least 8 characters.";
        if (!preg_match('/[a-z]/', $password))
            $errors[] = "Password must contain at least one lowercase letter.";
        if (!preg_match('/[A-Z]/', $password))
            $errors[] = "Password must contain at least one uppercase letter.";
        if (!preg_match('/[0-9]/', $password))
            $errors[] = "Password must contain at least one number.";
        if (!preg_match('/[\W_]/', $password))
            $errors[] = "Password must contain at least one special character.";
        return $errors;
    }
}