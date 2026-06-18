<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Both fields are required.';
    } else {
        try {
            $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ? OR email = ?');
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['role']      = $user['role'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username/email or password.';
            }
        } catch (mysqli_sql_exception $e) {
            error_log('Login error: ' . $e->getMessage());
            $error = 'An unexpected error occurred.';
        } finally {
            if (isset($stmt)) $stmt->close();
        }
    }
}
?>

<h2>Login</h2>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post">
    <label>Username or Email:</label>
    <input type="text" name="username" required>
    <label>Password:</label>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
<p>Don't have an account? <a href="register.php">Register</a></p>
<?php require_once 'includes/footer.php'; ?>