<?php
require_once 'config/db.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Get the recipe ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// 3. Fetch the recipe to verify ownership
$stmt = $pdo->prepare("SELECT user_id FROM recipes WHERE id = ?");
$stmt->execute([$id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    // Recipe doesn't exist
    header("Location: index.php");
    exit;
}

// 4. Check Permissions (Owner OR Admin)
$is_owner = ($recipe['user_id'] == $_SESSION['user_id']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

if ($is_owner || $is_admin) {
    // 5. Delete the recipe
    // Note: If you have Foreign Key constraints on 'likes' or 'comments', 
    // those need to be set to ON DELETE CASCADE in your MySQL table definition,
    // otherwise, manually delete them here first.
    
    // Optional: Delete related comments/likes first (manual cleanup)
    $pdo->prepare("DELETE FROM comments WHERE recipe_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM likes WHERE recipe_id = ?")->execute([$id]);
    
    // Delete the recipe
    $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt->execute([$id]);
}

// 6. Redirect back
// If we came from the admin panel, go back there. Otherwise go to profile.
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin.php') !== false) {
    header("Location: admin.php?msg=deleted");
} else {
    header("Location: profile.php?msg=deleted");
}
exit;