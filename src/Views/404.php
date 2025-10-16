<?php

use App\Auth\Auth;

$title = '404 Not Found';
ob_start();
?>

<div class="error-page">
    <div class="error-card">
        <h1 class="error-code">404</h1>
        <h2>Page Not Found</h2>
        <p>The page you're looking for doesn't exist.</p>
        <p class="error-hint">The URL might be incorrect or the page may have been removed.</p>
        <div class="error-actions">
            <?php if (Auth::check()) { ?>
                <a href="/dashboard" class="btn btn-primary">Go to Dashboard</a>
            <?php } else { ?>
                <a href="/login" class="btn btn-primary">Go to Login</a>
            <?php } ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>

