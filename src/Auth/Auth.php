<?php

namespace App\Auth;

use App\Config\App;
use App\Models\Role;
use App\Models\Session;
use App\Models\User;
use Random\RandomException;

class Auth
{
    private static ?array $user = null;
    private static ?string $token = null;

    /**
     * @throws RandomException
     */
    public static function attempt(string $username, string $password): bool
    {
        $user = User::findByUsername($username);

        if (!$user) {
            return false;
        }

        if (!User::verifyPassword($user, $password)) {
            return false;
        }

        // Create session
        $token = Session::createForUser($user['id']);

        // Set cookie
        self::setSessionCookie($token);

        // Cache user
        self::$user = $user;
        self::$token = $token;

        return true;
    }

    public static function logout(): void
    {
        $token = self::getToken();

        if ($token) {
            Session::deleteByToken($token);
        }

        self::clearSessionCookie();
        self::$user = null;
        self::$token = null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    public static function user(): ?array
    {
        if (self::$user !== null) {
            return self::$user;
        }

        $token = self::getToken();

        if (!$token) {
            return null;
        }

        $user = Session::getUserFromToken($token);

        if ($user) {
            self::$user = $user;
            self::$token = $token;

            // Refresh session expiration
            Session::refreshExpiration($token);
        }

        return $user;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user ? (int)$user['id'] : null;
    }

    public static function hasRole(string $roleName): bool
    {
        $user = self::user();

        if (!$user) {
            return false;
        }

        return User::hasRole($user['id'], $roleName);
    }

    public static function isAdmin(): bool
    {
        return self::hasRole(Role::ADMIN);
    }

    public static function can(string $permission): bool
    {
        $user = self::user();

        if (!$user) {
            return false;
        }

        $roleId = $user['role_id'];

        return Role::hasPermission($roleId, $permission);
    }

    private static function getToken(): ?string
    {
        if (self::$token) {
            return self::$token;
        }

        return $_COOKIE[App::get('session.cookie_name')] ?? null;
    }

    private static function setSessionCookie(string $token): void
    {
        $lifetime = App::getSessionLifetime();
        $cookieName = App::get('session.cookie_name');
        $secure = App::get('session.secure');
        $httponly = App::get('session.httponly');

        setcookie(
            $cookieName,
            $token,
            [
                'expires' => time() + $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => 'Lax',
            ]
        );
    }

    private static function clearSessionCookie(): void
    {
        $cookieName = App::get('session.cookie_name');

        setcookie(
            $cookieName,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );

        unset($_COOKIE[$cookieName]);
    }
}
