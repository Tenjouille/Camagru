<?php
// app/Controller/UserController.php

require_once __DIR__ . "/../Models/Users.php";
require_once __DIR__ . "/../Core/Mailer.php";

require_once __DIR__ . "/../Core/Session.php";
require_once __DIR__ . "/../Core/Request.php";
require_once __DIR__ . "/../Core/Validator.php";
require_once __DIR__ . "/../Core/Csrf.php";

class UserController {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login($method) {
        $errors = [];

        if ($method === 'POST') {
            Csrf::guard();
            $pseudo     = Request::post('pseudo');
            $password   = Request::post('password');

            $user = $this->user->findByPseudo($pseudo);
            if (!$user)
                $errors[] = "Echec de connexion : Mot de passe ou pseudonyme incorrect";
            else if (!password_verify($password, $user['password']))
                $errors[] = "Echec de connexion : Mot de passe ou pseudonyme incorrect";
            else if (!$user['is_verified'])
                $errors[] = "Compte pas encore authentifié. Verifiez votre boite mail";
            if (empty($errors)) {
                Session::set('user', $user);
                header('Location: /');
                exit;
            }
            // anti brute-force
            sleep(2);
        }
        $title = "Camagru - Connexion";
        $viewContent = __DIR__ ."/../Views/connection/login.php";
        require __DIR__ . "/../Views/layout.php";
    }

    public function register(string $method)
    {
        $errors = [];

        if ($method === 'POST') {
            Csrf::guard();
            $pseudo     = trim(Request::post('pseudo'));
            $email      = trim(Request::post('email'));
            $password   = Request::post('password');
            $confirm    = Request::post('confirm');

            if(strlen($pseudo) < 6 || strlen($pseudo) > 60)
                $errors[] = "Votre pseudo doit faire entre 6 et 60 caractères.";
            if(empty($email))
                $errors[] = "Un email est requis.";
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                $errors[] = "L'email est invalide.";
            $errors = [...$errors, ...Validator::password($password, '')];
            if($password !== $confirm)
                $errors[] = "Les mots de passe ne correspondent pas";
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
                
                header('Location: /login?registered=1');
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

        $title = "Camagru - Vérification du compte";
        $viewContent = __DIR__ . '/../Views/connection/verify.php';
        require __DIR__ . '/../Views/layout.php';
    }

    public function forgotPassword(string $method) {
        $errors = [];
        $success = false;

        if ($method === 'POST') {
            Csrf::guard();
            $email = trim(Request::post('email'));

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide";
            } else {
                $user = $this->user->findByEmail($email);

                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $this->user->setResetToken($email, $token);

                    $mailer = new Mailer();
                    $mailer->sendPasswordReset($email, $token);
                }

                $success = true;
            }
        }
        $title = "Camagru - Mot de passe oublié";
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
            Csrf::guard();
            $password = Request::post('password');
            $confirm = Request::post('confirm');

            if(strlen($password) < 8)
                $errors[] = "Un mot de passe de 8 caractères minimum est requit.";
            if($password !== $confirm)
                $errors[] = "Les mots de passe ne correspondent pas";

            if (empty($errors)) {
                $this->user->updatePassword($user['id'], $password);
                header('Location: /login?reset=1');
                exit;
            }
        }

        $title = "Camagru - Nouveau mot de passe";
        $viewContent = __DIR__ . "/../Views/connection/reset-password.php";
        require __DIR__ . "/../Views/layout.php";
    }

}