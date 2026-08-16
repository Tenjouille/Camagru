<?php

require_once __DIR__ . "/../app/Core/Session.php";
require_once __DIR__ . "/../app/Core/Request.php";

Session::start();
ob_start();

require_once __DIR__ . "/../app/Controllers/HomeController.php";
require_once __DIR__ . "/../app/Controllers/UserController.php";
require_once __DIR__ . "/../app/Controllers/GalleryController.php";
require_once __DIR__ . "/../app/Controllers/PostController.php";
require_once __DIR__ . "/../app/Controllers/ProfileController.php";

$uri = Request::uri();
$method = Request::method();
$user = Session::get('user');

switch ($uri) {
    case '/':
        if ($user)
            (new PostController())->create($method);
        else
            (new UserController())->login($method);
        break;
    case '/login':
        (new UserController())->login($method);
        break;
    case '/logout':
        (new UserController())->logout();
        break;
    case '/register':
        (new UserController())->register($method);
        break;
    case '/verify':
        (new UserController())->verify();
        break;
    case '/forgot-password':
        (new UserController())->forgotPassword($method);
        break;
    case '/reset-password':
        (new UserController())->resetPassword($method);
        break;
    case '/gallery':
        (new GalleryController())->index();
        break;
    case '/create':
        (new PostController())->create($method);
        break;
    case '/profile':
        (new ProfileController($user))->show();
        break;
    case '/profile/sticker/add':
        (new ProfileController($user))->addSticker();
        break;
    case '/profile/sticker/delete':
        (new ProfileController($user))->deleteSticker();
        break;
    case '/profile/pseudo':
        (new ProfileController($user))->updatePseudo();
        break;
    case '/profile/password':
        (new ProfileController($user))->updatePassword();
        break;
    case '/profile/notifications':
        (new ProfileController($user))->toggleNotifications();
        break;
    case '/post/like':
        (new PostController())->like();
        break;
    case '/post/comment':
        (new PostController())->comment();
        break;
    case '/post':
        (new PostController())->show();
        break;
    default:
        http_response_code(404);
        echo "404 - Page not found";
        break;
}
