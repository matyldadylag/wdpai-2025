<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Start user's session
session_start();

require 'Routing.php';

// Get the requested URL path and remove any query parameters
$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

// Run the router
Routing::getInstance()->run($path);
?>