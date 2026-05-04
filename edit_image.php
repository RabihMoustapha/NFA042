<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$imageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId  = $_SESSION['user_id'];
$error   = '';
$success = '';

// Fetch image and verify ownership
try {
    $stmt = $conn->prepare('SELECT id, title, image_path, user_id FROM images WHERE id = ?');
    $stmt->bind_param('i', $imageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $image  = $result->fetch_assoc();

    if (!$image || $image['user_id'] != $userId) {
        header('Location: gallery.php');
        exit();
    }
} catch (mysqli_sql_exception $e) {
    error_log('Edit image fetch error: ' . $e->getMessage());
    header('Location: gallery.php');
    exit();
} finally {
    if (isset($stmt)) $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTitle = trim($_POST['title'] ?? '');

    try {
        $update = $conn->prepare('UPDATE images SET title = ? WHERE id = ? AND user_id = ?');
        $update->bind_param('sii', $newTitle, $imageId, $userId);
        $update->execute();
        $success = 'Title updated.';
        $image['title'] = $newTitle;
    } catch (mysqli_sql_exception $e) {
        error_log('Edit image update error: ' . $e->getMessage());
        $error = 'Update failed.';
    } finally {
        if (isset($update)) $update->close();
    }
}
?>

<div class="nav">
    <strong>Edit Image</strong>
    <a href="gallery.php">← Back to Gallery</a>
    <a href="dashboard.php">Dashboard</a>
</div>

<h2>Edit Image: <?= htmlspecialchars($image['title'] ?: 'Untitled') ?></h2>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div style="margin-bottom: 1.5rem;">
    <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="Preview" style="max-width: 300px; border: 1px solid var(--border); border-radius: 3px;">
</div>

<form method="post">
    <label>Image Title:</label>
    <input type="text" name="title" value="<?= htmlspecialchars($image['title']) ?>" maxlength="100">
    <button type="submit">Save Changes</button>
</form>

<?php require_once 'includes/footer.php'; ?>