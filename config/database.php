<?php

require_once __DIR__ . "/env.php";
return [
    'host'      => $_ENV['DB_HOST'],
    'dbname'    => $_ENV['DB_NAME'],
    'user'      => $_ENV['DB_USER'],
    'password'  => $_ENV['DB_PASSWORD']
];