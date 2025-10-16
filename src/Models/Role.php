<?php

namespace App\Models;

use App\Database\Connection;

class Role extends Model
{
    public const string ADMIN = 'admin';
    public const string USER = 'user';
    public const string GUEST = 'guest';

    protected static string $table = 'roles';

    public static function getPermissions(int $roleId): array
    {
        $sql = 'SELECT permission_name FROM permissions WHERE role_id = :role_id';
        $results = Connection::fetchAll($sql, ['role_id' => $roleId]);

        return array_column($results, 'permission_name');
    }

    public static function hasPermission(int $roleId, string $permission): bool
    {
        $permissions = self::getPermissions($roleId);

        return in_array($permission, $permissions, true);
    }
}
