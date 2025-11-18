<?php
require_once '../config/db.php';

$stmt = $pdo->query("
    SELECT r.id, r.title, r.description, r.created_at, u.name AS author
    FROM recipes r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
")
$recipes = $stmt->fetchAll();

include '../includes/header.php';
?>

<h2>Latest Recipes</h2>
<?php if (!$recipes): ?>
    <p>No recipes yet.</p>
<?php else: ?>
    <?php> foreach ($recipes as $recipe): ?>
        <div class="recipe-card">
            <h3><a href="view_recipe.php?id=<?= $recipe['id'] ?>">
                <?= htmlspecialchars($recipe['title']) ?>
            </a></h3>
            <p>By <?= htmlspecialchars($recipe['author']) ?> on 
                <?= htmlspecialchars(date('F j, Y', strtotime($recipe['created_at']))) ?></p>
            <?php if ($recipe['description']): ?>
                <p><?= nl2br(htmlspecialchars(substr($recipe['description'], 0, 150))) ?>...</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>