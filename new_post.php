<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $game_name   = trim($_POST['game_name'] ?? '');
    $imagePath   = null;

    if (empty($title) || empty($game_name)) {
        $error = 'Title and game name are required.';
    } else {
        // Handle thumbnail upload (optional)
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['post_image'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
                $error = 'Only JPG, PNG, GIF images allowed.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = 'File size must be ≤ 2 MB.';
            } else {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $newName = uniqid('post_', true) . '.' . $ext;
                $uploadPath = 'images/' . $newName;
                if (!is_dir('images')) mkdir('images', 0755, true);
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $imagePath = $uploadPath;
                } else {
                    $error = 'Failed to upload image.';
                }
            }
        }

        if (!$error) {
            try {
                $stmt = $conn->prepare('INSERT INTO posts (user_id, title, description, game_name, image_path) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('issss', $_SESSION['user_id'], $title, $description, $game_name, $imagePath);
                $stmt->execute();
                $newId = $conn->insert_id;
                header("Location: post.php?id=$newId");
                exit();
            } catch (mysqli_sql_exception $e) {
                error_log('New post error: ' . $e->getMessage());
                $error = 'Failed to create post.';
                if ($imagePath && file_exists($imagePath)) unlink($imagePath);
            }
        }
    }
}
?>

<h2>Create New Topic</h2>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <label>Title *</label>
    <input type="text" name="title" required maxlength="150">

    <label>Game Name *</label>
    <input type="text" name="game_name" required maxlength="100" placeholder="e.g., Elden Ring">

    <label>Description (Problems, issues, discussion starters)</label>
    <textarea name="description" rows="6"></textarea>

    <label>Cover Image (optional)</label>
    <div class="custom-file">
        <input type="file" name="post_image" id="postImage" accept="image/jpeg,image/png,image/gif">
        <label for="postImage" class="file-label">Choose file</label>
        <span id="postFileName" class="file-name">No file selected</span>
    </div>

    <button type="submit">Publish Topic</button>
</form>

<script>
document.getElementById('postImage')?.addEventListener('change', function() {
    document.getElementById('postFileName').textContent = this.files.length ? this.files[0].name : 'No file selected';
});
</script>

<?php require_once 'includes/footer.php'; ?>