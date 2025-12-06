<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = $_POST['recipe_id'];
    $comment_text = trim($_POST['comment_text']);

    if (!empty($comment_text)) {
        // 1. Insert Comment
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, recipe_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $recipe_id, $comment_text]);

        // 2. Fetch Recipe Owner for Notification
        $stmt = $pdo->prepare("SELECT user_id FROM recipes WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);
        $recipe = $stmt->fetch();

        // 3. Insert Notification (If owner is not the commenter)
        if ($recipe && $recipe['user_id'] != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, actor_id, type, recipe_id) VALUES (?, ?, 'comment', ?)");
            $stmt->execute([$recipe['user_id'], $_SESSION['user_id'], $recipe_id]);
        }
    }
    
    // Redirect back to recipe
    header("Location: view_recipe.php?id=" . $recipe_id);
    exit;
}
?>