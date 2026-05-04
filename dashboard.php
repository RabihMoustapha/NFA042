<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare('SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (mysqli_sql_exception $e) {
    error_log('Dashboard fetch error: ' . $e->getMessage());
    $result = null;
}
?>

<div class="nav">
    <strong>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></strong>
    <a href="add_task.php">+ Add Task</a>
    <a href="logout.php">Logout</a>
    <a href="upload.php">Upload Image</a>
    <a href="gallery.php">Image Gallery</a>
</div>

<h2>My Tasks</h2>
<?php if (!$result || $result->num_rows === 0): ?>
    <p>No tasks yet. <a href="add_task.php">Create one</a>.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
        <?php while ($task = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($task['title']) ?></td>
            <td><?= htmlspecialchars($task['description']) ?></td>
            <td><?= htmlspecialchars($task['status']) ?></td>
            <td><?= htmlspecialchars($task['created_at']) ?></td>
            <td>
                <a href="edit_task.php?id=<?= (int)$task['id'] ?>" class="btn-edit">Edit</a>
                <a href="delete_task.php?id=<?= (int)$task['id'] ?>" class="btn-danger"
                   onclick="return confirm('Delete this task?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>
<?php require_once 'includes/footer.php'; ?>