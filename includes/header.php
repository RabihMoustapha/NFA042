<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamers Social</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="nav">
            <strong>🎮 Gamers Social</strong>
            <a href="dashboard.php">Feed</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="new_post.php">+ New Topic</a>
                <?php endif; ?>
                <span style="margin-left:auto; color:var(--text-secondary);">
                    <?= htmlspecialchars($_SESSION['username']) ?>
                </span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" style="margin-left:auto;">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>