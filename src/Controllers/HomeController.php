<?php

namespace App\Controllers;

use App\Auth\Auth;
use JetBrains\PhpStorm\NoReturn;

class HomeController
{
    #[NoReturn]
    public function index(): void
    {
        if (Auth::check()) {
            header('Location: /dashboard');
            exit;
        }

        header('Location: /login');
        exit;
    }

    public function forbidden(): void
    {
        http_response_code(403);
        require __DIR__ . '/../Views/403.php';
    }

    public function notFound(): void
    {
        http_response_code(404);
        require __DIR__ . '/../Views/404.php';
    }
}
