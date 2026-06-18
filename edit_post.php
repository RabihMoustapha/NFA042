<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Fetch post
$stmt = $conn->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->bind_param('i', $postId);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
if (!$post) {
    die('Post not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $game_name   = trim($_POST['game_name'] ?? '');
    $imagePath   = $post['image_path']; // keep old by default

    if (empty($title) || empty($game_name)) {
        $error = 'Title and game name are required.';
    } else {
        // Handle new image upload
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['post_image'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
                $error = 'Only JPG, PNG, GIF allowed.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = 'File too large.';
            } else {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $newName = uniqid('post_', true) . '.' . $ext;
                $uploadPath = 'images/' . $newName;
                if (!is_dir('images')) mkdir('images', 0755, true);
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Delete old image if exists
                    if ($imagePath && file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $imagePath = $uploadPath;
                } else {
                    $error = 'Upload failed.';
                }
            }
        }

        if (!$error) {
            try {
                $update = $conn->prepare('UPDATE posts SET title=?, description=?, game_name=?, image_path=? WHERE id=?');
                $update->bind_param('ssssi', $title, $description, $game_name, $imagePath, $postId);
                $update->execute();
                header("Location: post.php?id=$postId");
                exit();
            } catch (mysqli_sql_exception $e) {
                error_log('Edit post error: ' . $e->getMessage());
                $error = 'Update failed.';
                // If new image was moved but DB failed, delete it
                if (isset($uploadPath) && $uploadPath !== $post['image_path'] && file_exists($uploadPath)) {
                    unlink($uploadPath);
                }
            }
        }
    }
}
?>

<h2>Edit Topic</h2>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <label>Title *</label>
    <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required maxlength="150">

    <label>Game Name *</label>
    <input type="text" name="game_name" value="<?= htmlspecialchars($post['game_name']) ?>" required maxlength="100">

    <label>Description</label>
    <textarea name="description" rows="6"><?= htmlspecialchars($post['description']) ?></textarea>

    <label>Current Image:</label>
    <?php if ($post['image_path']): ?>
        <img src="<?= htmlspecialchars($post['image_path']) ?>" style="max-width:300px; display:block; margin-bottom:0.5rem;">
    <?php else: ?>
        <p>No image set.</p>
    <?php endif; ?>

    <label>Replace Image (optional)</label>
    <div class="custom-file">
        <input type="file" name="post_image" id="postImage" accept="image/jpeg,image/png,image/gif">
        <label for="postImage" class="file-label">Choose file</label>
        <span id="postFileName" class="file-name">No file selected</span>
    </div>

    <button type="submit">Update Topic</button>
</form>

<script>
document.getElementById('postImage')?.addEventListener('change', function() {
    document.getElementById('postFileName').textContent = this.files.length ? this.files[0].name : 'No file selected';
});
</script>

<?php require_once 'includes/footer.php'; ?>