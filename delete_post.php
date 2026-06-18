<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch image path to delete file
$stmt = $conn->prepare('SELECT image_path FROM posts WHERE id = ?');
$stmt->bind_param('i', $postId);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if ($post) {
    // Delete post (CASCADE will remove comments)
    $delete = $conn->prepare('DELETE FROM posts WHERE id = ?');
    $delete->bind_param('i', $postId);
    $delete->execute();

    // Delete image file if exists
    if ($post['image_path'] && file_exists($post['image_path'])) {
        unlink($post['image_path']);
    }
}

header('Location: dashboard.php');
exit();