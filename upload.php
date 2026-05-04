<?php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $file  = $_FILES['image'] ?? null;

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please select an image file.';
    } else {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime, $allowed)) {
            $error = 'Only JPG, PNG, GIF images are allowed.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $error = 'File size must be ≤ 2 MB.';
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $error = 'Invalid file extension.';
            } else {
                $newName    = uniqid('img_', true) . '.' . $ext;
                $uploadPath = 'uploads/' . $newName;

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }

                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    try {
                        $originalName = basename($file['name']); // keep original filename
                        $stmt = $conn->prepare(
                            'INSERT INTO images (user_id, title, image_path, original_filename) VALUES (?, ?, ?, ?)'
                        );
                        $stmt->bind_param('isss', $_SESSION['user_id'], $title, $uploadPath, $originalName);
                        $stmt->execute();
                        $message = 'Image uploaded successfully.';
                    } catch (mysqli_sql_exception $e) {
                        error_log('Upload DB error: ' . $e->getMessage());
                        $error = 'Database error, image not saved.';
                        unlink($uploadPath);
                    } finally {
                        if (isset($stmt)) $stmt->close();
                    }
                } else {
                    $error = 'Failed to move uploaded file.';
                }
            }
        }
    }
}
?>

<div class="nav">
    <strong>Upload Image</strong>
    <a href="dashboard.php">← Back to Dashboard</a>
    <a href="gallery.php">View Gallery</a>
</div>

<h2>Upload New Image</h2>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($message): ?>
    <div class="success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <label>Image Title (optional):</label>
    <input type="text" name="title" placeholder="e.g., Sunset photo" maxlength="100">

    <label>Select Image (JPG, PNG, GIF, max 2MB):</label>
    <div class="custom-file">
        <input type="file" name="image" id="imageFile" accept="image/jpeg,image/png,image/gif" required>
        <label for="imageFile" class="file-label">Choose file</label>
        <span id="file-name" class="file-name">No file selected</span>
    </div>

    <button type="submit">Upload</button>
</form>

<script>
const fileInput = document.getElementById('imageFile');
const fileNameSpan = document.getElementById('file-name');
fileInput.addEventListener('change', function() {
    fileNameSpan.textContent = this.files.length ? this.files[0].name : 'No file selected';
});
</script>

<?php require_once 'includes/footer.php'; ?>