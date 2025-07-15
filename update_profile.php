<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php?message=login_required");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

// Initialize variables
$name = $email = $photo_path = null;
$photo_path = isset($user['photo']) ? $user['photo'] : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validate input
    if (empty($name) || empty($email)) {
        header("Location: user_profile.php?error=empty_fields");
        exit;
    }

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'img/users/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file = $_FILES['photo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed_exts)) {
            $new_filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $full_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $full_path)) {
                // Delete old photo if it exists
                if ($photo_path && file_exists($photo_path)) {
                    unlink($photo_path);
                }
                $photo_path = $full_path;
            }
        }
    }

    // Update database
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, photo = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $photo_path, $user_id);
    $stmt->execute();

    // Update session
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['photo'] = $photo_path;

    header("Location: user_profile.php?message=profile_updated");
    exit;
}
?>

<!-- HTML Form for Profile Update -->
<form method="POST" enctype="multipart/form-data">
    <div>
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
    </div>
    <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>
    <div>
        <label for="photo">Profile Photo (Optional)</label>
        <input type="file" id="photo" name="photo" accept="image/*">
    </div>
    <button type="submit">Update Profile</button>
</form>
