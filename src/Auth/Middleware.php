<?php

namespace App\Auth;

class Middleware
{
    public static function requireAuth(): void
    {
        if (Auth::guest()) {
            self::redirect('/login');
            exit;
        }
    }

    public static function requireGuest(): void
    {
        if (Auth::check()) {
            self::redirect('/dashboard');
            exit;
        }
    }

    private static function redirect(string $path): void
    {
        header("Location: {$path}");
    }
}
