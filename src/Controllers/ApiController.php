<?php

namespace App\Controllers;

use App\Auth\Auth;
use App\Config\App;
use App\Models\Role;
use App\Models\User;

class ApiController
{
    /**
     * Public API endpoint - no authentication required.
     */
    public function getInfo(): array
    {
        return [
            'app_name' => App::get('app.name'),
            'version' => '1.0.0',
            'description' => 'Web application with role-based permissions',
            'features' => [
                'authentication' => true,
                'authorization' => true,
                'roles' => ['admin', 'user', 'guest'],
                'database' => 'PostgreSQL',
            ],
            'endpoints' => [
                'GET /api/info' => 'Get application information (public)',
                'GET /api/users' => 'Get all users (admin only)',
                'GET /api/me' => 'Get current user (authenticated)',
            ],
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get all users - admin only.
     */
    public function getUsers(): array
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);

            return [
                'error' => 'Forbidden',
                'message' => 'Admin access required',
            ];
        }

        $users = User::all();

        // Remove passwords from response
        $users = array_map(function ($user) {
            unset($user['password']);

            return $user;
        }, $users);

        return [
            'users' => $users,
            'total' => count($users),
        ];
    }

    /**
     * Get current authenticated user.
     */
    public function getCurrentUser(): array
    {
        if (!Auth::check()) {
            http_response_code(401);

            return [
                'error' => 'Unauthorized',
                'message' => 'Authentication required',
            ];
        }

        $user = Auth::user();
        $role = User::getRole($user['id']);

        // Remove password from response
        unset($user['password']);

        return [
            'user' => $user,
            'role' => $role,
        ];
    }

    /**
     * Get user statistics - admin only.
     */
    public function getStats(): array
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);

            return [
                'error' => 'Forbidden',
                'message' => 'Admin access required',
            ];
        }

        $totalUsers = User::count();
        $roles = Role::all();

        $roleStats = [];

        foreach ($roles as $role) {
            $roleStats[$role['name']] = count(User::where('role_id', $role['id']));
        }

        return [
            'total_users' => $totalUsers,
            'roles' => $roleStats,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }
}
