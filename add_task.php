<?php
require_once 'config/db.php';
require_once 'includes/header.php';
// session_start() removed – handled by header.php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    if (empty($title)) {
        $error = "Title is required.";
    } else {
        $query = "INSERT INTO tasks (user_id, title, description, status) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isss", $_SESSION['user_id'], $title, $description, $status);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Task added successfully.";
            $title = $description = '';
            $status = 'pending';
        } else {
            $error = "Failed to add task.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<h2>Add New Task</h2>
<?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
<form method="post">
    <label>Title *</label>
    <input type="text" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
    <label>Description</label>
    <textarea name="description" rows="4"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
    <label>Status</label>
    <select name="status">
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
    </select>
    <button type="submit">Save Task</button>
</form>
<a href="dashboard.php">← Back to Dashboard</a>

<?php require_once 'includes/footer.php'; ?>