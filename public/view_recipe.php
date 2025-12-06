<?php
require_once '../config/db.php';

$recipe_id = $_GET['id'] ?? null;
if (!$recipe_id) {
    header("Location: index.php");
    exit;
}

// 1. Fetch Recipe & Author
$stmt = $pdo->prepare("
    SELECT r.*, u.name AS author 
    FROM recipes r 
    JOIN users u ON r.user_id = u.user_id 
    WHERE r.recipe_id = ?
");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header("Location: index.php");
    exit;
}

// 2. Fetch Comments
$stmt = $pdo->prepare("
    SELECT c.*, u.name 
    FROM comments c 
    JOIN users u ON c.user_id = u.user_id 
    WHERE c.recipe_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->execute([$recipe_id]);
$comments = $stmt->fetchAll();

// 3. Check Like Status
$is_liked = false;
$like_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    if ($stmt->fetch()) $is_liked = true;
}

// Get total likes
$stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE recipe_id = ?");
$stmt->execute([$recipe_id]);
$like_count = $stmt->fetchColumn();

include '../includes/header.php';
?>

<div class="container">
    <h2><?= htmlspecialchars($recipe['title']) ?></h2>
    <p>
        By <?= htmlspecialchars($recipe['author']) ?> · 
        <?= htmlspecialchars(date('M j, Y', strtotime($recipe['created_at']))) ?>
    </p>

    <!-- Like Button Area -->
    <div style="margin: 20px 0;">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="toggle_like.php?id=<?= $recipe_id ?>" 
               style="background: <?= $is_liked ? '#e91e63' : '#ddd' ?>; color: <?= $is_liked ? '#fff' : '#333' ?>; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
               <?= $is_liked ? '♥ Liked' : '♡ Like' ?>
            </a>
        <?php else: ?>
            <a href="login.php" style="color: #666;">Login to Like</a>
        <?php endif; ?>
        <span style="margin-left: 10px;"><strong><?= $like_count ?></strong> Likes</span>
    </div>

    <hr>

    <?php if (!empty($recipe['description'])): ?>
        <h3>Description</h3>
        <p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
    <?php endif; ?>

    <h3>Ingredients</h3>
    <p><?= nl2br(htmlspecialchars($recipe['ingredients'])) ?></p>

    <h3>Steps</h3>
    <p><?= nl2br(htmlspecialchars($recipe['steps'])) ?></p>
    
    <hr>
    
    <!-- Comments Section -->
    <h3>Comments (<?= count($comments) ?>)</h3>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="add_comment.php" method="post" style="margin-bottom: 30px;">
            <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">
            <textarea name="comment_text" rows="3" required placeholder="Write a comment..." style="width: 100%; padding: 10px;"></textarea>
            <button type="submit" style="margin-top: 10px; padding: 8px 16px;">Post Comment</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Login</a> to post a comment.</p>
    <?php endif; ?>

    <div class="comments-list">
        <?php foreach ($comments as $comment): ?>
            <div style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 5px;">
                <strong><?= htmlspecialchars($comment['name']) ?></strong> 
                <span style="font-size: 0.8em; color: #777;"> on <?= date('M j, Y', strtotime($comment['created_at'])) ?></span>
                <p style="margin: 5px 0 0;"><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></p>
                
                <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $comment['user_id'] || isset($_SESSION['is_admin']) && $_SESSION['is_admin'])): ?>
                    <a href="delete_comment.php?id=<?= $comment['comment_id'] ?>" 
                       style="color: red; font-size: 0.8em; float: right;"
                       onclick="return confirm('Delete comment?')">Delete</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
