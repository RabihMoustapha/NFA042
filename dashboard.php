<?php
require_once 'config/db.php';
require_once 'includes/header.php';

try {
    $result = $conn->query('SELECT * FROM posts ORDER BY created_at DESC');
} catch (mysqli_sql_exception $e) {
    error_log('Feed error: ' . $e->getMessage());
    $result = null;
}
?>

<h2>Latest Discussions</h2>

<?php if (!$result || $result->num_rows === 0): ?>
    <p>No topics yet. <?= isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? '<a href="new_post.php">Create the first one</a>.' : 'Check back later.' ?></p>
<?php else: ?>
    <div class="feed-grid">
        <?php while ($post = $result->fetch_assoc()): ?>
            <div class="card">
                <?php if (!empty($post['image_path'])): ?>
                    <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                <?php endif; ?>
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <div class="game-name">🎮 <?= htmlspecialchars($post['game_name']) ?></div>
                <div class="desc"><?= nl2br(htmlspecialchars(mb_substr($post['description'], 0, 120))) ?>...</div>
                <div class="actions">
                    <a href="post.php?id=<?= $post['id'] ?>" class="btn">View & Comment</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-edit">Edit</a>
                        <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger"
                           onclick="return confirm('Delete this topic and all its comments?')">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>