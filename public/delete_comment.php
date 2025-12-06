<?php
require_once 'config/db.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Get the comment ID
$id = $_GET['id'] ?? null;
if (!$id) {
    // Redirect to previous page or home
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header("Location: $redirect");
    exit;
}

// 3. Fetch the comment to verify permissions
$stmt = $pdo->prepare("SELECT user_id FROM comments WHERE comment_id = ?");
$stmt->execute([$id]);
$comment = $stmt->fetch();

if (!$comment) {
    // Comment doesn't exist
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header("Location: $redirect");
    exit;
}

// 4. Check Permissions (Owner OR Admin)
$is_owner = ($comment['user_id'] == $_SESSION['user_id']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

if ($is_owner || $is_admin) {
    // 5. Delete the comment
    $stmt = $pdo->prepare("DELETE FROM comments WHERE comment_id = ?");
    $stmt->execute([$id]);
}

// 6. Redirect back
// If we came from the admin panel, go back there.
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin.php') !== false) {
    header("Location: admin.php?msg=comment_deleted");
} elseif (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: index.php");
}
exit;