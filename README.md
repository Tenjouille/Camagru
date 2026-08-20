# Camagru

Camagru est une application web de type "photobooth" : capture depuis la webcam (ou upload d'une photo), superposition de stickers, publication dans une galerie publique avec likes et commentaires, et notifications par email. Projet PHP en MVC "maison" (sans framework), conteneurisé avec Docker/Podman.

## Fonctionnalités

- Inscription / connexion avec vérification de compte par email, réinitialisation de mot de passe
- Capture webcam (ou choix d'une photo si la webcam n'est pas disponible) + superposition d'un ou plusieurs stickers, redimensionnables et rotatifs
- Galerie paginée de tous les posts, avec likes et commentaires
- Notification par email au propriétaire d'un post lorsqu'un commentaire est ajouté (activable/désactivable depuis le profil)
- Gestion de stickers personnels (upload, suppression)
- Gestion de compte (pseudo, email, mot de passe, préférences de notification)

## Stack technique

| Composant | Rôle |
|---|---|
| PHP 8.3 + Apache (`php:8.3-apache`) | Backend, sert `public/` comme document root |
| MySQL 8.0 | Base de données |
| Mailhog | Capteur d'emails pour le développement local (aucun email n'est réellement envoyé) |
| phpMyAdmin | Interface d'administration de la base, en local uniquement |
| Bootstrap 5 / Bootstrap Icons (CDN) | UI |
| JavaScript natif (modules ES) | Pas de framework front |

## Architecture MVC

```
app/
├── Controllers/     # une classe par groupe de routes, orchestrent Models + Views
│   ├── UserController.php      # inscription, connexion, mot de passe oublié/reset, vérification
│   ├── ProfileController.php   # compte (pseudo/email/mdp/notifs), stickers personnels
│   ├── PostController.php      # création de post, like, commentaire, suppression
│   └── GalleryController.php   # galerie paginée
├── Models/          # une classe par table, requêtes SQL préparées (PDO)
│   ├── Users.php (class User), Posts.php (class Post)
│   ├── Comments.php (class Comment), Likes.php (class Like), Stickers.php (class Sticker)
├── Core/            # briques transverses, pas liées à une table/route précise
│   ├── Database.php    # connexion PDO (singleton)
│   ├── Session.php     # wrapper $_SESSION + génération du token CSRF
│   ├── Request.php     # wrapper $_GET/$_POST/$_FILES/headers
│   ├── Csrf.php         # vérification du token CSRF (voir Sécurité)
│   ├── Validator.php    # règles de validation réutilisables (mot de passe)
│   └── Mailer.php       # envoi d'email (vérification, reset, notification de commentaire)
└── Views/           # templates PHP, aucune logique métier
    ├── layout.php           # squelette HTML commun (head, header, footer, CSRF_TOKEN)
    ├── connection/          # login, register, forgot/reset-password, verify
    ├── partials/            # header.php (nav), footer.php
    ├── create.php, gallery.php, profile.php, home.php

public/
├── index.php        # point d'entrée unique, routeur (switch sur l'URI)
├── .htaccess        # réécrit toute URL vers index.php (mod_rewrite)
└── assets/
    ├── css/style.css
    ├── js/
    │   ├── main.js          # global (toggle menu mobile), chargé sur toutes les pages
    │   ├── utils.js          # escapeHtml(), partagé
    │   ├── create/            # capture webcam + stickers (modules ES)
    │   ├── profile/           # formulaires du compte + stickers personnels (modules ES)
    │   └── gallery/           # modale post, like, commentaires (modules ES)
    ├── uploads/posts/         # photos publiées (générées, gitignored)
    └── stickers/               # images de stickers (certaines générées, gitignored)
```

Pas de routeur "magique" : `public/index.php` fait un simple `switch` sur l'URI et instancie le bon contrôleur — volontairement explicite plutôt qu'abstrait, pour rester lisible sans framework.

## Fichiers de configuration

| Fichier | Rôle |
|---|---|
| `docker-compose.yml` | Définit les 4 services : `web` (PHP/Apache, port 8080), `db` (MySQL, port 3306), `phpmyadmin` (port 8081), `mailhog` (SMTP 1025, UI web 8025) |
| `docker/php/Dockerfile` | Image PHP : extensions (`gd`, `pdo_mysql`, `mysqli`), durcissement `php.ini` (`display_errors = Off`, `log_errors = On`), redirection du document root vers `public/`, activation de `mod_rewrite` |
| `docker/php/entrypoint.sh` | Lancé au démarrage du conteneur : force les droits d'écriture sur `public/uploads` et `public/assets/stickers` (nécessaire en Podman rootless, où le bind mount appartient à l'utilisateur host et pas à `www-data`), puis démarre Apache |
| `docker/php/ssmtp.conf` | Configure `ssmtp` (utilisé par la fonction PHP `mail()`) pour relayer tous les emails vers `mailhog:1025` au lieu d'un vrai serveur SMTP — aucun email ne quitte la machine en local |
| `public/.htaccess` | Réécrit toute requête qui ne correspond pas à un fichier/dossier réel vers `index.php`, pour laisser le routeur PHP gérer l'URL |
| `config/database.php` / `config/env.php` | Chargent `.env` à la main (pas de dépendance Composer) et exposent la config DB à `Database.php` |
| `.env` *(non versionné)* | Identifiants de la base de données — voir [Lancer le projet](#lancer-le-projet) |
| `docker/db/init/schema.sql` | Schéma SQL + données de seed, exécuté automatiquement à la première création du conteneur `db` |

## Sécurité

| Module | Où | Détail |
|---|---|---|
| Protection CSRF | `app/Core/Csrf.php`, `Session::csrfToken()` | Token aléatoire par session, vérifié (`hash_equals`, temps constant) sur chaque action POST qui modifie l'état — en champ caché pour les formulaires classiques, en header `X-CSRF-Token` pour les appels `fetch()` |
| Échappement XSS | `public/assets/js/utils.js` (`escapeHtml`), vues PHP (`htmlspecialchars`) | Tout contenu utilisateur (commentaires, pseudos) est échappé avant insertion dans le DOM ou le HTML rendu |
| Cookie de session durci | `app/Core/Session.php` | `httponly` (invisible à `document.cookie`) + `samesite=Lax` (limite l'envoi cross-site) — `secure` à activer une fois servi en HTTPS |
| Headers de sécurité | `public/index.php` | `X-Frame-Options: DENY` (anti-clickjacking), `X-Content-Type-Options: nosniff` (anti MIME-sniffing) |
| Throttle sur le login | `UserController::login()` | 2s de délai après chaque tentative échouée, pour ralentir le brute-force |
| Mots de passe | `Users` model | Hashés avec `password_hash()` (bcrypt), jamais stockés/loggés en clair |
| Requêtes SQL | tous les Models | Requêtes préparées PDO (`PDO::ATTR_EMULATE_PREPARES => false`), aucune concaténation de valeurs utilisateur dans le SQL |
| Validation des uploads | `ProfileController::addSticker()` | Vérifie le MIME réel (`getimagesize`, pas l'extension), plafonne la taille (2 Mo), déduplique par hash SHA-256 |
| Erreurs PHP | `docker/php/Dockerfile` | `display_errors` désactivé, erreurs loguées côté serveur plutôt qu'affichées au visiteur |

## Lancer le projet

Prérequis : Docker ou Podman + leur `compose` (le projet est développé et testé avec Podman rootless, via `podman-compose`).

1. Cloner le repo, puis créer un fichier `.env` à la racine :
   ```
   DB_HOST=db
   DB_NAME=camagru
   DB_USER=camagru_user
   DB_PASSWORD=un_mot_de_passe
   DB_ROOT_PASSWORD=un_autre_mot_de_passe
   ```
2. Démarrer les conteneurs :
   ```
   podman-compose up --build
   ```
   (ou `docker-compose up --build`)
3. Accéder à l'application :

   | URL | Service |
   |---|---|
   | http://localhost:8080 | Camagru |
   | http://localhost:8081 | phpMyAdmin |
   | http://localhost:8025 | Interface web Mailhog (tous les emails envoyés par l'app atterrissent ici) |

Au premier démarrage, `schema.sql` crée automatiquement les tables et un compte administrateur. Pour repartir d'une base vierge : `podman-compose down -v` (supprime aussi le volume de la base) avant de relancer `up --build`.

Aucun rebuild n'est nécessaire pour les changements PHP/JS/CSS (`public/` et `app/` sont montés en bind mount) — seul un changement dans `docker/php/Dockerfile` ou `entrypoint.sh` demande `--build`.
