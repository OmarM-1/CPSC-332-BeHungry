<?php
require_once 'config/db.php';

// 1. Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success_msg = "";

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    // Validation
    if ($name === '') {
        $errors[] = "Name cannot be empty.";
    }

    // Image Upload Logic
    $image_path = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_type = mime_content_type($file_tmp);
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Only JPG, PNG, and WEBP images are allowed.";
        } else {
            // Create uploads directory if it doesn't exist
            $upload_dir = 'uploads/profiles/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($file_tmp, $destination)) {
                $image_path = $destination;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // If no errors, update database
    if (empty($errors)) {
        try {
            if ($image_path) {
                // Update with new image
                $stmt = $pdo->prepare("UPDATE users SET name = ?, bio = ?, profile_image_url = ? WHERE id = ?");
                $stmt->execute([$name, $bio, $image_path, $user_id]);
            } else {
                // Update without changing image
                $stmt = $pdo->prepare("UPDATE users SET name = ?, bio = ? WHERE id = ?");
                $stmt->execute([$name, $bio, $user_id]);
            }

            // Update session name if changed
            $_SESSION['name'] = $name;
            $success_msg = "Profile updated successfully!";
            
            // Refresh parent page logic (optional redirect)
            // header("Location: profile.php"); // Uncomment to redirect immediately
            
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// 3. Fetch Current User Data (Pre-fill form)
$stmt = $pdo->prepare("SELECT name, bio, profile_image_url FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

include 'includes/header.php';
?>

<div class="form-container" style="max-width: 600px; margin: 0 auto;">
    <h2>Edit Profile</h2>

    <?php if ($success_msg): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <?= htmlspecialchars($success_msg) ?> <a href="profile.php">Return to Profile</a>
        </div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <?php foreach ($errors as $e): ?>
                <p style="margin: 0;"><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        
        <!-- Current Image Preview -->
        <div style="text-align: center; margin-bottom: 20px;">
            <?php if ($user['profile_image_url']): ?>
                <img src="<?= htmlspecialchars($user['profile_image_url']) ?>" alt="Current Profile" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
            <?php else: ?>
                <div style="width: 100px; height: 100px; background: #ddd; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold;">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>

        <label for="name">Display Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required style="width: 100%; padding: 8px; margin-bottom: 15px;">

        <label for="bio">Bio</label>
        <textarea id="bio" name="bio" rows="4" style="width: 100%; padding: 8px; margin-bottom: 15px;"><?= htmlspecialchars($user['bio']) ?></textarea>

        <label for="profile_image">Profile Picture (JPG, PNG, WEBP)</label>
        <input type="file" id="profile_image" name="profile_image" accept="image/jpeg, image/png, image/webp" style="margin-bottom: 20px;">

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Save Changes</button>
            <a href="profile.php" style="text-decoration: none; background-color: #6c757d; color: white; padding: 10px 20px; border-radius: 5px;">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>