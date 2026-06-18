<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $body      = trim($_POST['body'] ?? '');
    $imagePath = null;

    if (empty($body)) {
        $error = 'Comment cannot be empty.';
    } else {
        // Handle file upload for comment image (optional)
        if (isset($_FILES['comment_image']) && $_FILES['comment_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['comment_image'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($mime, $allowedTypes)) {
                $error = 'Only JPG, PNG, GIF images are allowed.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = 'File size must be ≤ 2 MB.';
            } else {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $error = 'Invalid file extension.';
                } else {
                    $newName = uniqid('comment_', true) . '.' . $ext;
                    $uploadPath = 'images/' . $newName;
                    if (!is_dir('images')) {
                        mkdir('images', 0755, true);
                    }
                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        $imagePath = $uploadPath;
                    } else {
                        $error = 'Failed to upload image.';
                    }
                }
            }
        }

        if (!$error) {
            try {
                $stmt = $conn->prepare('INSERT INTO comments (post_id, user_id, body, image_path) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('iiss', $postId, $_SESSION['user_id'], $body, $imagePath);
                $stmt->execute();
                $success = 'Comment added!';
                // Redirect to avoid form resubmission
                header("Location: post.php?id=$postId");
                exit();
            } catch (mysqli_sql_exception $e) {
                error_log('Comment insert error: ' . $e->getMessage());
                $error = 'Failed to add comment.';
                if ($imagePath && file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
    }
}

// Fetch the post
try {
    $stmt = $conn->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->bind_param('i', $postId);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();
    if (!$post) {
        die('Post not found.');
    }
} catch (mysqli_sql_exception $e) {
    error_log('Post fetch error: ' . $e->getMessage());
    die('Error loading post.');
}

// Fetch comments with usernames
try {
    $stmt2 = $conn->prepare('SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC');
    $stmt2->bind_param('i', $postId);
    $stmt2->execute();
    $comments = $stmt2->get_result();
} catch (mysqli_sql_exception $e) {
    error_log('Comments fetch error: ' . $e->getMessage());
    $comments = null;
}
?>

<div class="nav">
    <a href="dashboard.php">← Back to Feed</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-edit">Edit Topic</a>
        <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger"
           onclick="return confirm('Delete this topic and all its comments?')">Delete Topic</a>
    <?php endif; ?>
</div>

<!-- Post display -->
<h2><?= htmlspecialchars($post['title']) ?></h2>
<p><strong>Game:</strong> <?= htmlspecialchars($post['game_name']) ?></p>
<?php if (!empty($post['image_path'])): ?>
    <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Post image" style="max-width:100%; border-radius:4px; margin:1rem 0;">
<?php endif; ?>
<p><?= nl2br(htmlspecialchars($post['description'])) ?></p>

<hr style="margin:2rem 0; border-color: var(--border);">

<!-- Comments section -->
<h3>Comments</h3>

<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<?php if ($comments && $comments->num_rows > 0): ?>
    <?php while ($comment = $comments->fetch_assoc()): ?>
        <div class="comment">
            <div class="meta">
                <strong><?= htmlspecialchars($comment['username']) ?></strong> · <?= htmlspecialchars($comment['created_at']) ?>
                <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $comment['user_id'] || $_SESSION['role'] === 'admin')): ?>
                    · <a href="delete_comment.php?id=<?= $comment['id'] ?>&post_id=<?= $postId ?>" class="btn btn-danger" style="padding:0.2rem 0.5rem; font-size:0.7rem;" onclick="return confirm('Delete this comment?')">Delete</a>
                <?php endif; ?>
            </div>
            <p><?= nl2br(htmlspecialchars($comment['body'])) ?></p>
            <?php if (!empty($comment['image_path'])): ?>
                <a href="<?= htmlspecialchars($comment['image_path']) ?>" target="_blank">
                    <img src="<?= htmlspecialchars($comment['image_path']) ?>" alt="Comment image">
                </a>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No comments yet. Be the first!</p>
<?php endif; ?>

<!-- Comment form (only for logged-in users) -->
<?php if (isset($_SESSION['user_id'])): ?>
    <h3 style="margin-top:2rem;">Add a Comment</h3>
    <form method="post" enctype="multipart/form-data">
        <label>Your Comment:</label>
        <textarea name="body" required></textarea>

        <label>Attach an Image (optional, max 2MB):</label>
        <div class="custom-file">
            <input type="file" name="comment_image" id="commentImage" accept="image/jpeg,image/png,image/gif">
            <label for="commentImage" class="file-label">Choose file</label>
            <span id="commentFileName" class="file-name">No file selected</span>
        </div>

        <button type="submit">Post Comment</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Login</a> to comment.</p>
<?php endif; ?>

<script>
document.getElementById('commentImage')?.addEventListener('change', function() {
    document.getElementById('commentFileName').textContent = this.files.length ? this.files[0].name : 'No file selected';
});
</script>

<?php require_once 'includes/footer.php'; ?>    