<?php
require_once 'config/db.php';
require_once 'includes/header.php';  // uses your existing header

// Check if user is logged in (optional – adjust to your auth system)
// If your project has login, uncomment below:
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $file = $_FILES['image'] ?? null;

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $error = "Please select an image file.";
    } else {
        // Validate file type
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            $error = "Only JPG, PNG, GIF images are allowed.";
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $error = "File size must be ≤ 2MB.";
        } else {
            // Create unique filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newName = uniqid() . '_' . time() . '.' . $ext;
            $uploadPath = 'uploads/' . $newName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Save to database
                $stmt = mysqli_prepare($conn, "INSERT INTO images (title, image_path) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt, "ss", $title, $uploadPath);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Image uploaded successfully.";
                } else {
                    $error = "Database error. Image not saved.";
                    // Delete uploaded file if DB fails
                    unlink($uploadPath);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "Failed to move uploaded file.";
            }
        }
    }
}
?>

<h2>Upload New Image</h2>

<?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($message): ?>
    <div class="success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <label>Image Title (optional):</label>
    <input type="text" name="title" placeholder="e.g., Sunset photo">

    <label>Select Image (JPG, PNG, GIF, max 2MB):</label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/gif" required>

    <button type="submit">Upload</button>
</form>

<p><a href="gallery.php">View Gallery →</a></p>

<?php require_once 'includes/footer.php'; ?>