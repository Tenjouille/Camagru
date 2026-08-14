<?php
// app/Controller/GalleryController

require_once __DIR__ . "/../Models/Posts.php";

require_once __DIR__ . "/../Core/Session.php";
require_once __DIR__ . "/../Core/Request.php";


class GalleryController
{
    private Post $post;

    public function __construct() {
        $this->post = new Post();
    }

    public function index() {
        $page = max(1, (int) Request::get('page', 1));

        $posts = $this->post->getPaginated($page);
        $totalPages = $this->post->getTotalPages();

        $title = "Camagru - Gallery";
        $viewContent = __DIR__ . '/../Views/gallery.php';
        require __DIR__ . '/../Views/layout.php';
    }
}