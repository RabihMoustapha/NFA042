<?php
require_once 'config/db.php';
require_once 'includes/header.php';

try {
    $result = $conn->query('SELECT id, user_id, title, image_path, created_at FROM images ORDER BY created_at DESC');
} catch (mysqli_sql_exception $e) {
    error_log('Gallery query error: ' . $e->getMessage());
    $result = null;
}
?>

<!-- Navigation bar -->
<div class="nav">
    <strong>Image Gallery</strong>
    <a href="dashboard.php">← Back to Dashboard</a>
    <a href="upload.php">+ Upload New Image</a>
</div>

<h2>All Images</h2>

<?php if (!$result || $result->num_rows === 0): ?>
    <p>No images yet. <a href="upload.php">Upload your first image</a>.</p>
<?php else: ?>
    <div class="gallery-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
                $imgPath   = htmlspecialchars($row['image_path']);
                $title     = htmlspecialchars($row['title'] ?: 'Untitled');
                $imageId   = (int)$row['id'];
                $ownerId   = (int)$row['user_id'];
                $isOwner   = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $ownerId;
            ?>
            <div class="card gallery-item">
                <img src="<?= $imgPath ?>" alt="<?= $title ?>" class="gallery-image">
                <h3 class="gallery-title"><?= $title ?></h3>
                <p class="gallery-date">
                    <?= htmlspecialchars(date('Y-m-d H:i', strtotime($row['created_at']))) ?>
                </p>
                <div class="card-actions">
                    <a href="download.php?id=<?= $imageId ?>" class="btn">⬇ Download</a>
                    <?php if ($isOwner): ?>
                        <a href="edit_image.php?id=<?= $imageId ?>" class="btn btn-edit">✎ Edit</a>
                        <a href="delete_image.php?id=<?= $imageId ?>" class="btn btn-danger"
                           onclick="return confirm('Delete this image?')">✕ Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>