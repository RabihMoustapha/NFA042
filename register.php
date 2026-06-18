<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (mb_strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        try {
            $check = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $check->bind_param('ss', $username, $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = 'Username or email already taken.';
            } else {
                $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // Find the smallest available ID (gap or next)
                $gapResult = $conn->query(
                    "SELECT COALESCE(MIN(t1.id + 1), 1) AS next_id
                     FROM users t1
                     LEFT JOIN users t2 ON t1.id + 1 = t2.id
                     WHERE t2.id IS NULL"
                );
                $gapRow = $gapResult->fetch_assoc();
                $nextId = (int)$gapRow['next_id'];

                // Insert with explicit ID
                $stmt = $conn->prepare('INSERT INTO users (id, username, email, password) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('isss', $nextId, $username, $email, $hashed);
                $stmt->execute();

                $success = 'Registration successful. <a href="login.php">Login here</a>';
            }
        } catch (mysqli_sql_exception $e) {
            error_log('Register error: ' . $e->getMessage());
            $error = 'An unexpected error occurred.';
        } finally {
            if (isset($check)) $check->close();
            if (isset($stmt)) $stmt->close();
        }
    }
}
?>

<h2>Register</h2>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
<form method="post">
    <label>Username:</label>
    <input type="text" name="username" required maxlength="50">
    <label>Email:</label>
    <input type="email" name="email" required>
    <label>Password:</label>
    <input type="password" name="password" required>
    <label>Confirm Password:</label>
    <input type="password" name="confirm_password" required>
    <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="login.php">Login</a></p>
<?php require_once 'includes/footer.php'; ?>