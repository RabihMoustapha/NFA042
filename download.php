<?php
require_once 'config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    die('Invalid image ID.');
}

try {
    $stmt = $conn->prepare('SELECT image_path, original_filename FROM images WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row    = $result->fetch_assoc();
} catch (mysqli_sql_exception $e) {
    error_log('Download fetch error: ' . $e->getMessage());
    http_response_code(500);
    die('Service unavailable.');
} finally {
    if (isset($stmt)) $stmt->close();
}

if (!$row) {
    http_response_code(404);
    die('Image not found.');
}

// Build absolute path and validate it stays inside project
$baseDir  = realpath(__DIR__);
$filePath = realpath($row['image_path']);

if ($filePath === false || strpos($filePath, $baseDir) !== 0) {
    http_response_code(403);
    die('Access denied.');
}

if (!is_file($filePath)) {
    http_response_code(404);
    die('File not found on server.');
}

// Use original filename if available, otherwise fall back to stored name
$downloadName = !empty($row['original_filename'])
                ? $row['original_filename']
                : basename($filePath);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: must-revalidate');
header('Pragma: public');
readfile($filePath);
exit();