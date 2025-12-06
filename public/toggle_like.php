<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$recipe_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($recipe_id) {
    // 1. Check if already liked
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$user_id, $recipe_id]);
    $existing_like = $stmt->fetch();

    if ($existing_like) {
        // UNLIKE: Remove record
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$user_id, $recipe_id]);
    } else {
        // LIKE: Add record
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $recipe_id]);

        // 2. Fetch Owner for Notification
        $stmt = $pdo->prepare("SELECT user_id FROM recipes WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);
        $recipe = $stmt->fetch();

        // 3. Insert Notification
        if ($recipe && $recipe['user_id'] != $user_id) {
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, actor_id, type, recipe_id) VALUES (?, ?, 'like', ?)");
            $stmt->execute([$recipe['user_id'], $user_id, $recipe_id]);
        }
    }
}

header("Location: view_recipe.php?id=" . $recipe_id);
exit;
?>