<?php
// app/Controllers/HomeController.php

class   HomeController
{
    public function index() {
        $title = "Camagru - Accueil";
        $viewContent = __DIR__ . '/../Views/home.php';
        require __DIR__ .'/../Views/layout.php';
    }
}