<?php

// Define a constant for the project root directory
define('PROJECT_ROOT', dirname(__DIR__));

// Load composer autoload first so classes (App\*) can be resolved
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad(); // Use safeLoad to avoid errors if .env is missing in production

// Start session for admin auth
session_start();

// Load routes (routes will create the Router, define routes, then dispatch)
require_once __DIR__ . '/../src/Routes/web.php';