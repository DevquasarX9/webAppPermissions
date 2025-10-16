<?php

/**
 * Application Entry Point.
 *
 * This is the main entry point for the web application.
 * All requests are routed through this file.
 */

// Load Composer autoloader
use App\Config\App;
use App\Controllers\ApiController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Router;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        putenv($line);
        list($name, $value) = explode('=', $line, 2);
        $_ENV[$name] = $value;
    }
}

// Error handling
if (App::isDebug()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Create router instance
$router = new Router();

// Define routes
// Home
$router->get('/', function () {
    $controller = new HomeController();
    $controller->index();
});

// Authentication routes
$router->get('/login', function () {
    $controller = new AuthController();
    $controller->showLoginForm();
});

$router->post('/login', function () {
    $controller = new AuthController();
    $controller->login();
});

$router->post('/logout', function () {
    $controller = new AuthController();
    $controller->logout();
});

// Dashboard routes
$router->get('/dashboard', function () {
    $controller = new DashboardController();
    $controller->index();
});

// Error pages
$router->get('/403', function () {
    $controller = new HomeController();
    $controller->forbidden();
});

$router->get('/404', function () {
    $controller = new HomeController();
    $controller->notFound();
});

// API routes
$router->get('/api/info', function () {
    return new ApiController()->getInfo();
});

$router->get('/api/users', function () {
    return new ApiController()->getUsers();
});

$router->get('/api/me', function () {
    return new ApiController()->getCurrentUser();
});

$router->get('/api/stats', function () {
    return new ApiController()->getStats();
});

// Dispatch the router
try {
    $router->dispatch();
} catch (Exception $e) {
    if (App::isDebug()) {
        echo '<h1>Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>Internal Server Error</h1>';
    }
}
