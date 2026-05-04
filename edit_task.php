<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Fetch existing task
try {
    $stmt = $conn->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task   = $result->fetch_assoc();
} catch (mysqli_sql_exception $e) {
    error_log('Edit task fetch error: ' . $e->getMessage());
    $task = null;
} finally {
    if (isset($stmt)) $stmt->close();
}

if (!$task) {
    header('Location: dashboard.php');
    exit();
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status      = $_POST['status'] ?? 'pending';

    if (empty($title)) {
        $error = 'Title is required.';
    } elseif (!in_array($status, ['pending', 'completed'])) {
        $error = 'Invalid status.';
    } else {
        try {
            $update = $conn->prepare('UPDATE tasks SET title=?, description=?, status=? WHERE id=? AND user_id=?');
            $update->bind_param('sssii', $title, $description, $status, $task_id, $user_id);
            $update->execute();
            $success = 'Task updated.';
            // Update local copy to show updated values
            $task['title']       = $title;
            $task['description'] = $description;
            $task['status']      = $status;
        } catch (mysqli_sql_exception $e) {
            error_log('Edit task error: ' . $e->getMessage());
            $error = 'Update failed.';
        } finally {
            if (isset($update)) $update->close();
        }
    }
}
?>

<!-- Navigation bar – consistent with other pages -->
<div class="nav">
    <strong>Edit Task</strong>
    <a href="dashboard.php">← Back to Dashboard</a>
</div>

<h2>Edit Task</h2>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
    <label>Title *</label>
    <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required maxlength="100">

    <label>Description</label>
    <textarea name="description" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>

    <label>Status</label>
    <select name="status">
        <option value="pending"  <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
    </select>

    <button type="submit">Update Task</button>
</form>

<?php require_once 'includes/footer.php'; ?>