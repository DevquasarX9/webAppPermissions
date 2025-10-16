<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'webAppPermissions'; ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php if (App\Auth\Auth::check()) { ?>
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-brand">
                    <a href="/dashboard">webAppPermissions</a>
                </div>
                <ul class="nav-menu">
                    <li><a href="/dashboard">Dashboard</a></li>
                    <?php if (App\Auth\Auth::isAdmin()) { ?>
                        <li><a href="/dashboard">Admin Panel</a></li>
                    <?php } ?>
                    <li class="nav-user">
                        <span>👤 <?php echo htmlspecialchars(App\Auth\Auth::user()['username']); ?></span>
                    </li>
                    <li>
                        <form action="/logout" method="POST" style="display: inline;">
                            <button type="submit" class="btn btn-secondary">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    <?php } ?>

    <main class="container">
        <?php echo $content ?? ''; ?>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> webAppPermissions - Built without frameworks</p>
    </footer>
</body>
</html>

