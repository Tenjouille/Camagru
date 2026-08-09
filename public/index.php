<?php

session_start();
ob_start();

require_once __DIR__ . "/../app/Controllers/HomeController.php";
require_once __DIR__ . "/../app/Controllers/UserController.php";
require_once __DIR__ . "/../app/Controllers/GalleryController.php";
require_once __DIR__ . "/../app/Controllers/PostController.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$user = $_SESSION['user'] ?? null;

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
