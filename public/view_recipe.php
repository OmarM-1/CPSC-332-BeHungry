<?php
require_once '../config/db.php';

$recipe_id = $_GET['id'] ?? null;
if (!$recipe_id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.*, u.name AS author
    FROM recipes r
    JOIN users u ON r.user_id = u.id
    WHERE r.id = ?
");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header("Location: index.php");
    exit;
}

include '../includes/header.php';
?>

<h2><?= htmlspecialchars($recipe['title']) ?></h2>
<p>
    By <?= htmlspecialchars($recipe['author']) ?> · 
    <?= htmlspecialchars($recipe['created_at']) ?>
</p>

<?php if (!empty($recipe['category'])): ?>
    <p><strong>Category:</strong> <?= htmlspecialchars($recipe['category']) ?></p>
<?php endif; ?>

<?php if (!empty($recipe['description'])): ?>
    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
<?php endif; ?>

<h3>Ingredients</h3>
<p><?= nl2br(htmlspecialchars($recipe['ingredients'])) ?></p>

<h3>Steps</h3>
<p><?= nl2br(htmlspecialchars($recipe['steps'])) ?></p>

<?php include '../includes/footer.php'; ?>
