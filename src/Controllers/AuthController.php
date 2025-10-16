<?php

namespace App\Controllers;

use App\Auth\Auth;
use App\Auth\Middleware;
use JetBrains\PhpStorm\NoReturn;
use Random\RandomException;

class AuthController
{
    public function showLoginForm(): void
    {
        Middleware::requireGuest();
        require __DIR__ . '/../Views/login.php';
    }

    /**
     * @throws RandomException
     */
    public function login(): void
    {
        Middleware::requireGuest();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Username and password are required';
            require __DIR__ . '/../Views/login.php';

            return;
        }

        if (Auth::attempt($username, $password)) {
            header('Location: /dashboard');
            exit;
        }

        $error = 'Invalid credentials';
        require __DIR__ . '/../Views/login.php';
    }

    #[NoReturn]
    public function logout(): void
    {
        Middleware::requireAuth();
        Auth::logout();
        header('Location: /login');
        exit;
    }
}
