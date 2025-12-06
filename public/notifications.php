<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Fetch Notifications
$stmt = $pdo->prepare("
    SELECT n.*, u.name as actor_name, r.title as recipe_title
    FROM notifications n
    JOIN users u ON n.actor_id = u.user_id
    JOIN recipes r ON n.recipe_id = r.recipe_id
    WHERE n.user_id = ?
    ORDER BY n.created_at DESC
    LIMIT 50
");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

// 2. Mark all as read (Simple implementation: mark read when page loads)
$update = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
$update->execute([$user_id]);

include 'includes/header.php';
?>

<div class="container" style="max-width: 800px;">
    <h2>Notifications</h2>

    <?php if (empty($notifications)): ?>
        <p>No notifications yet.</p>
    <?php else: ?>
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($notifications as $n): ?>
                <li style="
                    padding: 15px; 
                    border-bottom: 1px solid #eee; 
                    background: <?= $n['is_read'] ? '#fff' : '#f0f8ff' ?>;
                ">
                    <strong><?= htmlspecialchars($n['actor_name']) ?></strong>
                    <?php if ($n['type'] === 'like'): ?>
                        liked your recipe 
                    <?php else: ?>
                        commented on your recipe 
                    <?php endif; ?>
                    
                    <a href="view_recipe.php?id=<?= $n['recipe_id'] ?>">
                        <?= htmlspecialchars($n['recipe_title']) ?>
                    </a>
                    
                    <div style="font-size: 0.8em; color: #888; margin-top: 5px;">
                        <?= date('M j, Y g:i a', strtotime($n['created_at'])) ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>