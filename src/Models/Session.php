<?php

namespace App\Models;

use App\Config\App;
use App\Database\Connection;
use Random\RandomException;

class Session extends Model
{
    protected static string $table = 'sessions';

    public static function create(array $data): ?int
    {
        // Clean up expired sessions before creating new one
        self::cleanupExpired();

        return parent::create($data);
    }

    /**
     * @throws RandomException
     */
    public static function createForUser(int $userId): string
    {
        // Generate secure random token
        $token = bin2hex(random_bytes(32));

        // Calculate expiration time
        $lifetime = App::getSessionLifetime();
        $expiresAt = date('Y-m-d H:i:s', time() + $lifetime);

        // Delete any existing sessions for this user (single session per user)
        self::deleteByUserId($userId);

        // Create new session
        self::create([
            'user_id' => $userId,
            'session_token' => $token,
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }

    public static function deleteByToken(string $token): bool
    {
        $sql = 'DELETE FROM sessions WHERE session_token = :token';

        return Connection::execute($sql, ['token' => $token]);
    }

    public static function deleteByUserId(int $userId): bool
    {
        $sql = 'DELETE FROM sessions WHERE user_id = :user_id';

        return Connection::execute($sql, ['user_id' => $userId]);
    }

    public static function cleanupExpired(): void
    {
        $sql = 'DELETE FROM sessions WHERE expires_at < NOW()';
        Connection::execute($sql);
    }

    public static function getUserFromToken(string $token): ?array
    {
        $sql = 'SELECT u.* FROM users u 
                INNER JOIN sessions s ON s.user_id = u.id 
                WHERE s.session_token = :token AND s.expires_at > NOW() 
                LIMIT 1';

        return Connection::fetch($sql, ['token' => $token]);
    }

    public static function refreshExpiration(string $token): bool
    {
        $lifetime = App::getSessionLifetime();
        $expiresAt = date('Y-m-d H:i:s', time() + $lifetime);

        $sql = 'UPDATE sessions SET expires_at = :expires_at WHERE session_token = :token';

        return Connection::execute($sql, [
            'expires_at' => $expiresAt,
            'token' => $token,
        ]);
    }
}
