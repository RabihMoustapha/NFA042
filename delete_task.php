<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

$query = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: dashboard.php");
exit();
?>