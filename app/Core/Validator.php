<?php
// app/Core/Validator.php

class Validator
{
    private function __construct() {}

    public static function password($password, $default = '') {
        $errors = [];
        $password ??= $default;
        if (strlen($password) < 8)
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        if (!preg_match('/[a-z]/', $password))
            $errors[] = "Le mot de passe doit contenir au moins une minuscule.";
        if (!preg_match('/[A-Z]/', $password))
            $errors[] = "Le mot de passe doit contenir au moins une majuscule.";
        if (!preg_match('/[0-9]/', $password))
            $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
        if (!preg_match('/[\W_]/', $password))
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial.";
        return $errors;
    }
}