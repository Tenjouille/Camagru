<?php

// app/Controller/ProfileController

require_once __DIR__ . "/../Models/Stickers.php";
require_once __DIR__ . "/../Models/Users.php";

require_once __DIR__ . "/../Core/Session.php";
require_once __DIR__ . "/../Core/Request.php";

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
    public function show() {
        $errors = [];
        $success = null;

        $currentUser = $this->currentUser;
        $stickers = $this->sticker->getAvailable($currentUser['id']);

        $title = "Camagru - Profil";
        $viewContent = __DIR__ . '/../Views/profile.php';
        require __DIR__ . '/../Views/layout.php';
    }

    // POST /profile/sticker — upload d'un sticker personnel
    public function addSticker() {

    }

    // POST /profile/pseudo — changement de pseudo
    public function updatePseudo() {
        $errors = [];
        $new_pseudo = Request::post('pseudo');
        if (strlen($new_pseudo) < 6)
            $errors[] = "Le nouveau pseudo doit faire au moins 6 caractères";
        $this->user->updatePseudo(Session::get('user')['id'], $new_pseudo);
    
    
        $user = Session::get('user');
        $user['pseudo'] = $new_pseudo;
        Session::set('user', $user);

        if (Request::isAjax()) {
            http_response_code(204);
            exit;
        }
        header('Location: /profile');
        exit;
    }
    // POST /profile/password — changement de mot de passe (utilisateur connecté)
    public function updatePassword() {

    }

    // POST /profile/notifications — activer/désactiver les notifs email sur commentaire
    public function toggleNotifications() {
        $is_enabled = Request::has('email_notification') ? 1 : 0;
        $this->user->toggleEmailNotification($this->currentUser['id'], $is_enabled);

        $user = Session::get('user');
        $user['email_notification'] = $is_enabled;
        Session::set('user', $user);

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
