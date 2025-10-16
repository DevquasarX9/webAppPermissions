<?php

namespace App\Models;

use App\Database\Connection;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByUsername(string $username): ?array
    {
        return self::findBy('username', $username);
    }

    public static function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, $user['password']);
    }

    public static function getRole(int $userId): ?array
    {
        $sql = 'SELECT r.* FROM roles r 
                INNER JOIN users u ON u.role_id = r.id 
                WHERE u.id = :user_id LIMIT 1';

        return Connection::fetch($sql, ['user_id' => $userId]);
    }

    public static function hasRole(int $userId, string $roleName): bool
    {
        $role = self::getRole($userId);

        return $role && $role['name'] === $roleName;
    }
}
