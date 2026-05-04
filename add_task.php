<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error   = '';
$success = '';
$title   = '';
$description = '';
$status  = 'pending';

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
            $stmt = $conn->prepare('INSERT INTO tasks (user_id, title, description, status) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('isss', $_SESSION['user_id'], $title, $description, $status);
            $stmt->execute();
            $success = 'Task added successfully.';
            // Clear form after success
            $title = $description = '';
            $status = 'pending';
        } catch (mysqli_sql_exception $e) {
            error_log('Add task error: ' . $e->getMessage());
            $error = 'Failed to add task.';
        } finally {
            if (isset($stmt)) $stmt->close();
        }
    }
}
?>

<!-- Navigation bar – consistent with other pages -->
<div class="nav">
    <strong>Add New Task</strong>
    <a href="dashboard.php">← Back to Dashboard</a>
</div>

<h2>Task Details</h2>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
    <label>Title *</label>
    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required maxlength="100">

    <label>Description</label>
    <textarea name="description" rows="4"><?= htmlspecialchars($description) ?></textarea>

    <label>Status</label>
    <select name="status">
        <option value="pending"  <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
    </select>

    <button type="submit">Save Task</button>
</form>

<?php require_once 'includes/footer.php'; ?>