<?php
$title = '403 Forbidden';
ob_start();
?>

<div class="error-page">
    <div class="error-card">
        <h1 class="error-code">403</h1>
        <h2>Forbidden</h2>
        <p>You don't have permission to access this resource.</p>
        <p class="error-hint">This page requires specific role or permission that you don't have.</p>
        <div class="error-actions">
            <a href="/dashboard" class="btn btn-primary">Go to Dashboard</a>
            <a href="/login" class="btn btn-secondary">Login</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>

