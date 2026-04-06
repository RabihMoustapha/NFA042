<?php
require_once 'config/db.php';
require_once 'includes/header.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Fetch task and verify ownership
$query = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$task = mysqli_fetch_assoc($result);

if (!$task) {
    header("Location: dashboard.php");
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
        $update = "UPDATE tasks SET title = ?, description = ?, status = ? WHERE id = ? AND user_id = ?";
        $stmt_up = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt_up, "sssii", $title, $description, $status, $task_id, $user_id);
        if (mysqli_stmt_execute($stmt_up)) {
            $success = "Task updated.";
            // Refresh task data
            $task['title'] = $title;
            $task['description'] = $description;
            $task['status'] = $status;
        } else {
            $error = "Update failed.";
        }
        mysqli_stmt_close($stmt_up);
    }
}
?>

<h2>Edit Task</h2>
<?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
<form method="post">
    <label>Title *</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
    <label>Description</label>
    <textarea name="description" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
    <label>Status</label>
    <select name="status">
        <option value="pending" <?php echo $task['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
        <option value="completed" <?php echo $task['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
    </select>
    <button type="submit">Update Task</button>
</form>
<a href="dashboard.php">← Back to Dashboard</a>

<?php require_once 'includes/footer.php'; ?>