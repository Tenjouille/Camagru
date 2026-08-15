<?php
// app/Controller/UserController.php

require_once __DIR__ . "/../Models/Users.php";
require_once __DIR__ . "/../Core/Mailer.php";

require_once __DIR__ . "/../Core/Session.php";
require_once __DIR__ . "/../Core/Request.php";
require_once __DIR__ . "/../Core/Validator.php";

class UserController {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login($method) {
        $errors = [];

        if ($method === 'POST') {
            $pseudo     = Request::post('pseudo');
            $password   = Request::post('password');

            $user = $this->user->findByPseudo($pseudo);
            if (!$user)
                $errors[] = "Invalid Identifiers : incorrect password or username";
            else if (!password_verify($password, $user['password']))
                $errors[] = "Invalid Identifiers : incorrect password or username";
            else if (!$user['is_verified'])
                $errors[] = "Account not verified yet. Please, check your mailbox";
            if (empty($errors)) {
                Session::set('user', $user);
                header('Location: /');
                exit;
            }
        }
        $title = "Camagru - Login";
        $viewContent = __DIR__ ."/../Views/connection/login.php";
        require __DIR__ . "/../Views/layout.php";
    }

    public function register(string $method)
    {
        $errors = [];

        if ($method === 'POST') {
            $pseudo     = trim(Request::post('pseudo'));
            $email      = trim(Request::post('email'));
            $password   = Request::post('password');
            $confirm    = Request::post('confirm');

            if(strlen($pseudo) < 6)
                $errors[] = "Votre pseudo doit faire entre 6 et 60 caractères.";
            if(empty($email))
                $errors[] = "Un email est requis.";
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors[] = "L'email est invalide.";
            $errors = [...$errors, ...Validator::password($password, '')];
            if($password !== $confirm)
                $errors[] = "Passwords doesn't match";
            if ($this->user->findByEmail($email))
                $errors[] = "Un utilisateur utilise déjà cette adresse email.";
            if ($this->user->findByPseudo($pseudo))
                $errors[] = "Un utilisateur utilise déjà ce pseudonyme.";
        
            if (empty($errors)) {
                $this->user->create($pseudo, $email, $password);
                
                $token = bin2hex(random_bytes(32));
                $this->user->setVerificationToken($email, $token);

                $mailer = new Mailer();
                $mailer->sendVerification($email, $token);
                
                header('Location: /login?registered=1'); // équivalent d'un window.location.href = /login?registered=1 en JS
                exit;
            }
        }
        $title = "Camagru - Inscription";
        $viewContent = __DIR__ . "/../Views/connection/register.php";
        require __DIR__ . "/../Views/layout.php";
    }

    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function verify() {
        $token = Request::get('token');

        if (!$token) {
            header('Location: /login');
            exit;
        }

        $success = $this->user->verifyAccount($token);

        $title = "Account verification";
        $viewContent = __DIR__ . '/../Views/connection/verify.php';
        require __DIR__ . '/../Views/layout.php';
    }

    public function forgotPassword(string $method) {
        $errors = [];
        $success = false;

        if ($method === 'POST') {
            $email = trim(Request::post('email'));

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email is required";
            } else {
                $user = $this->user->findByEmail($email);

                // We do not reveal if the email exist or not (security measure)
                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $this->user->setResetToken($email, $token);

                    $mailer = new Mailer();
                    $mailer->sendPasswordReset($email, $token);
                }

                $success = true;
            }
        }
        $title = "Forgotten Password";
        $viewContent = __DIR__ . "/../Views/connection/forgot-password.php";
        require __DIR__ . '/../Views/layout.php';
    }

    public function resetPassword(string $method) {
        $errors = [];
        $token = Request::get('token');

        if (!$token) {
            header('Location: /login');
            exit;
        }

        $user = $this->user->findByResetToken($token);

        if (!$user) {
            header('Location: /login');
            exit;
        }

        if ($method === 'POST') {
            $password = Request::post('password');
            $confirm = Request::post('confirm');

            if(strlen($password) < 8)
                $errors[] = "A minimum 8 character password is required.";
            if($password !== $confirm)
                $errors[] = "Passwords doesn't match";

            if (empty($errors)) {
                $this->user->updatePassword($user['id'], $password);
                header('Location: /login?reset=1');
                exit;
            }
        }

        $title = "New Password";
        $viewContent = __DIR__ . "/../Views/connection/reset-password.php";
        require __DIR__ . "/../Views/layout.php";
    }

}