<?php

namespace App\Config;

class App
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return match ($key) {
            'app.name' => $_ENV['APP_NAME'] ?? 'webAppPermissions',
            'app.env' => $_ENV['APP_ENV'] ?? 'development',
            'app.debug' => $_ENV['APP_DEBUG'] ?? true,
            'session.lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 3600), // 1 hour default
            'session.cookie_name' => 'webapp_session',
            'session.secure' => $_ENV['SESSION_SECURE'] ?? false,
            'session.httponly' => true,
            default => $default,
        };
    }

    public static function isDebug(): bool
    {
        return (bool)self::get('app.debug');
    }

    public static function getSessionLifetime(): int
    {
        return self::get('session.lifetime');
    }
}
