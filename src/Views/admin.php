<?php
$title = 'Admin Dashboard';
ob_start();
?>

<div class="dashboard">
    <h1>Admin Dashboard</h1>
    <p class="subtitle">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</p>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="stat-number"><?php echo $totalUsers; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Roles</h3>
            <div class="stat-number"><?php echo count($roles); ?></div>
        </div>
        <div class="stat-card">
            <h3>Your Role</h3>
            <div class="stat-badge">
                <span class="badge badge-admin">Admin</span>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>All Users</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u) { ?>
                    <?php $userRole = App\Models\Role::find($u['role_id']); ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($userRole['name']); ?>">
                                <?php echo htmlspecialchars($userRole['name']); ?>
                            </span>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($u['created_at'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Available Roles</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Users Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $r) { ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($r['name']); ?>">
                                <?php echo htmlspecialchars($r['name']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($r['description']); ?></td>
                        <td><?php echo count(App\Models\User::where('role_id', $r['id'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Admin API Endpoints</h2>
        <p>As an admin, you have access to:</p>
        <ul class="api-list">
            <li>
                <code>GET <a href="/api/info" target="_blank">/api/info</a></code>
                <span class="api-public">Public</span>
            </li>
            <li>
                <code>GET <a href="/api/me" target="_blank">/api/me</a></code>
                <span class="api-auth">Authenticated</span>
            </li>
            <li>
                <code>GET <a href="/api/users" target="_blank">/api/users</a></code>
                <span class="api-admin">Admin Only</span>
            </li>
            <li>
                <code>GET <a href="/api/stats" target="_blank">/api/stats</a></code>
                <span class="api-admin">Admin Only</span>
            </li>
        </ul>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>

