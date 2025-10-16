<?php

namespace App\Controllers;

use App\Auth\Auth;
use App\Auth\Middleware;
use App\Models\Role;
use App\Models\User;

class DashboardController
{
    public function index(): void
    {
        Middleware::requireAuth();

        $user = Auth::user();
        $role = User::getRole($user['id']);

        // Route to appropriate dashboard based on role
        if ($role['name'] === Role::ADMIN) {
            $this->adminDashboard();
        } elseif ($role['name'] === Role::USER) {
            $this->userDashboard();
        } else {
            $this->guestDashboard();
        }
    }

    private function adminDashboard(): void
    {
        $user = Auth::user();
        $users = User::all();
        $totalUsers = User::count();
        $roles = Role::all();

        require __DIR__ . '/../Views/admin.php';
    }

    private function userDashboard(): void
    {
        $user = Auth::user();
        $role = User::getRole($user['id']);

        require __DIR__ . '/../Views/dashboard.php';
    }

    private function guestDashboard(): void
    {
        $user = Auth::user();
        $role = User::getRole($user['id']);

        require __DIR__ . '/../Views/dashboard.php';
    }
}
