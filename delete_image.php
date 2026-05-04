<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$imageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId  = $_SESSION['user_id'];

try {
    // Fetch image path and verify ownership
    $stmt = $conn->prepare('SELECT image_path, user_id FROM images WHERE id = ?');
    $stmt->bind_param('i', $imageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row    = $result->fetch_assoc();

    if (!$row) {
        die('Image not found.');
    }

    if ($row['user_id'] != $userId) {
        die('You do not have permission to delete this image.');
    }

    // Delete record
    $delete = $conn->prepare('DELETE FROM images WHERE id = ?');
    $delete->bind_param('i', $imageId);
    $delete->execute();

    // Remove file
    $filePath = $row['image_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

} catch (mysqli_sql_exception $e) {
    error_log('Delete image error: ' . $e->getMessage());
    die('An error occurred.');
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($delete)) $delete->close();
}

header('Location: gallery.php');
exit();
?>