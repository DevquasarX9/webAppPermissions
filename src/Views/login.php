<?php
$title = 'Login';
ob_start();
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Login</h1>
        <p class="subtitle">Welcome to webAppPermissions</p>

        <?php if (isset($error)) { ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form method="POST" action="/login">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <div class="auth-info">
            <h3>Test Accounts:</h3>
            <ul>
                <li><strong>Admin:</strong> username: <code>admin</code>, password: <code>admin123</code></li>
                <li><strong>User:</strong> username: <code>user</code>, password: <code>user123</code></li>
                <li><strong>Guest:</strong> username: <code>guest</code>, password: <code>guest123</code></li>
            </ul>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>

