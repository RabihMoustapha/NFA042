<?php
require_once 'config/db.php';
require_once 'includes/header.php';  // starts session

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $task_id, $user_id);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log('Delete task error: ' . $e->getMessage());
} finally {
    if (isset($stmt)) $stmt->close();
}

header('Location: dashboard.php');
exit();