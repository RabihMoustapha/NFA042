<?php
require_once 'config/db.php';

// Get image ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid request.");
}

// Fetch image path from database
$stmt = mysqli_prepare($conn, "SELECT image_path FROM images WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Image not found.");
}

$filePath = $row['image_path'];

// Security: prevent directory traversal
$realBase = realpath(__DIR__);
$realFile = realpath($filePath);
if ($realFile === false || strpos($realFile, $realBase) !== 0) {
    die("Access denied.");
}

if (!file_exists($filePath)) {
    die("File not found on server.");
}

// Force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: must-revalidate');
header('Pragma: public');
readfile($filePath);
exit();
?>