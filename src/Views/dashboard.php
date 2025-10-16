<?php
$title = 'Dashboard';
ob_start();
?>

<div class="dashboard">
    <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    
    <div class="card">
        <h2>Your Profile</h2>
        <table class="info-table">
            <tr>
                <th>Username:</th>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <th>Role:</th>
                <td><span class="badge badge-<?php echo strtolower($role['name']); ?>"><?php echo htmlspecialchars($role['name']); ?></span></td>
            </tr>
            <tr>
                <th>User ID:</th>
                <td><?php echo $user['id']; ?></td>
            </tr>
            <tr>
                <th>Member Since:</th>
                <td><?php echo date('F j, Y', strtotime($user['created_at'])); ?></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h2>Your Permissions</h2>
        <p>As a <strong><?php echo htmlspecialchars($role['name']); ?></strong>, you have access to:</p>
        <ul class="permissions-list">
            <?php
            $permissions = App\Models\Role::getPermissions($user['role_id']);

if (empty($permissions)) {
    ?>
                <li>No specific permissions assigned</li>
            <?php } else { ?>
                <?php foreach ($permissions as $permission) { ?>
                    <li>✓ <?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($permission))); ?></li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>

    <div class="card">
        <h2>API Information</h2>
        <p>You can access the public API endpoint:</p>
        <div class="code-block">
            <code>GET <a href="/api/info" target="_blank">/api/info</a></code>
        </div>
        <p>Authenticated users can also access:</p>
        <div class="code-block">
            <code>GET <a href="/api/me" target="_blank">/api/me</a></code>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>

