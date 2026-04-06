<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Fetch all images
$query = "SELECT id, title, image_path, created_at FROM images ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<h2>Image Gallery</h2>

<?php if (mysqli_num_rows($result) === 0): ?>
    <p>No images yet. <a href="upload.php">Upload your first image</a>.</p>
<?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card" style="text-align: center;">
                <?php 
                $imgPath = htmlspecialchars($row['image_path']);
                $title = htmlspecialchars($row['title'] ?: 'Untitled');
                ?>
                <img src="<?php echo $imgPath; ?>" alt="<?php echo $title; ?>" 
                     style="width: 100%; height: 180px; object-fit: cover; border-radius: var(--radius-md);">
                <h3 style="margin: 0.75rem 0 0.5rem; font-size: 1rem;"><?php echo $title; ?></h3>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                    <?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?>
                </p>
                <a href="download.php?id=<?php echo $row['id']; ?>" class="btn">⬇ Download</a>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<p style="margin-top: 2rem;"><a href="upload.php">+ Upload Another Image</a></p>

<?php require_once 'includes/footer.php'; ?>