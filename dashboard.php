<?php
require_once 'config/db.php';
require_once 'includes/header.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all tasks for this user
$query = "SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="nav">
    <strong>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></strong>
    <a href="add_task.php">+ Add Task</a>
    <a href="logout.php">Logout</a>
</div>

<h2>My Tasks</h2>
<?php if (mysqli_num_rows($result) === 0): ?>
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
        <?php while ($task = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($task['title']); ?></td>
            <td><?php echo htmlspecialchars($task['description']); ?></td>
            <td><?php echo $task['status']; ?></td>
            <td><?php echo $task['created_at']; ?></td>
            <td>
                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn-edit">Edit</a>
                <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="btn-danger" onclick="return confirm('Delete this task?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>