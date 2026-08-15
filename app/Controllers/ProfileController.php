<?php

// app/Controller/ProfileController

require_once __DIR__ . "/../Models/Stickers.php";
require_once __DIR__ . "/../Models/Users.php";

require_once __DIR__ . "/../Core/Session.php";
require_once __DIR__ . "/../Core/Request.php";
require_once __DIR__ . "/../Core/Validator.php";

class ProfileController
{
    private User $user;
    private Sticker $sticker;
    private array $currentUser;

    public function __construct(?array $currentUser) {
        if (!$currentUser) {
            header('Location: /login');
            exit;
        }

        $this->user = new User();
        $this->sticker = new Sticker();
        $this->currentUser = $currentUser;
    }

    // GET /profile — affiche la page (infos compte, préférence notifs, stickers perso)
    public function show(array $errors = []) {
        $success = null;

        $currentUser = $this->currentUser;
        $stickers = $this->sticker->getAvailable($currentUser['id']);

        $title = "Camagru - Profil";
        $viewContent = __DIR__ . '/../Views/profile.php';
        require __DIR__ . '/../Views/layout.php';
    }

    private function refreshCurrentUser(): void {
        $this->currentUser = $this->user->findById($this->currentUser['id']);
        Session::set('user', $this->currentUser);
}

    private function returnErrors(array $errors = []) {
        if (Request::isAjax()) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(['errors' => $errors]);
            exit;
        }
        $this->show($errors);
    }

    // POST /profile/sticker — upload d'un sticker personnel
    public function addSticker() {

    }

    // POST /profile/pseudo — changement de pseudo
    public function updatePseudo() {
        $errors = [];
        $new_pseudo = Request::post('pseudo');
        $exists = $this->user->findByPseudo($new_pseudo);
        if (strlen($new_pseudo) < 6 || strlen($new_pseudo) > 60)
            $errors[] = "Le nouveau pseudo doit faire entre 6 et 60 caractères";
        if ($exists && (int) $exists['id'] !== (int) $this->currentUser['id'])
            $errors[] = "Pseudo déjà utilisé par un autre utilisateur";
        if (!empty($errors)) {
            $this->returnErrors($errors);
            return;
        }
        $this->user->updatePseudo(Session::get('user')['id'], $new_pseudo);
        $this->refreshCurrentUser();
        if (Request::isAjax()) {
            http_response_code(204);
            exit;
        }
        header('Location: /profile');
        exit;
    }
    // POST /profile/password — changement de mot de passe (utilisateur connecté)
    public function updatePassword() {
        $errors = [];
        $curr_pwd = Request::post('current_password'); 
        $new_pwd = Request::post('password');
        $match_pwd = Request::post('confirm');

        if (!$curr_pwd || !$new_pwd || !$match_pwd) {
            $errors[] = "Vous devez remplir les trois champs pour pouvoir changer de mot de passe";
            $this->returnErrors($errors);
            return;
        }
        if (!password_verify($curr_pwd, $this->currentUser['password']))
            $errors[] = "Mot de passe incorrect";
        $errors = [...$errors, ...Validator::password($new_pwd)];
        if ($new_pwd !== $match_pwd)
            $errors[] = "Les mots de passe ne correspondent pas";

        if (!empty($errors)) {
            $this->returnErrors($errors);
            return;
        }
        $this->user->updatePassword($this->currentUser['id'], $new_pwd);

        $this->refreshCurrentUser();

        if (Request::isAjax()) {
            http_response_code(204);
            exit;
        }
        header('Location: /profile');
        exit;
    }

    // POST /profile/notifications — activer/désactiver les notifs email sur commentaire
    public function toggleNotifications() {
        $is_enabled = Request::has('email_notification') ? 1 : 0;
        $this->user->toggleEmailNotification($this->currentUser['id'], $is_enabled);

        $this->refreshCurrentUser();

        // Requête AJAX (toggle sans rechargement) : pas de redirection, juste un statut.
        if (Request::isAjax()) {
            http_response_code(204);
            exit;
        }

        // Fallback si JS est désactivé : comportement classique par redirection.
        header('Location: /profile');
        exit;
    }
}
