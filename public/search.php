<?php
require_once '../config/db.php';

$query = trim($_GET['q'] ?? '');

$recipes = [];
if ($query !== '') {
    $sql = "
        SELECT r.id, r.title, r.description, r.category, r.created_at, u.name AS author
        FROM recipes r
        JOIN users u ON r.user_id = u.id
        WHERE r.title LIKE :term
           OR r.ingredients LIKE :term
           OR r.category LIKE :term
        ORDER BY r.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $term = '%' . $query . '%';
    $stmt->bindParam(':term', $term, PDO::PARAM_STR);
    $stmt->execute();
    $recipes = $stmt->fetchAll();
}

include '../includes/header.php';
?>

<h2>Search Results</h2>

<form action="search.php" method="get" style="margin-bottom:1rem;">
    <input 
        type="text" 
        name="q" 
        placeholder="Search recipes..." 
        value="<?= htmlspecialchars($query) ?>"
    >
    <button type="submit">Search</button>
</form>

<?php if ($query === ''): ?>
    <p>Type something above to search by recipe name, ingredients, or category.</p>

<?php elseif (!$recipes): ?>
    <p>No recipes found matching <strong><?= htmlspecialchars($query) ?></strong>.</p>

<?php else: ?>
    <p>Found <?= count($recipes) ?> result(s) for <strong><?= htmlspecialchars($query) ?></strong>:</p>

    <?php foreach ($recipes as $recipe): ?>
        <div class="recipe-card">
            <h3>
                <a href="view_recipe.php?id=<?= $recipe['id'] ?>">
                    <?= htmlspecialchars($recipe['title']) ?>
                </a>
            </h3>
            <p>
                By <?= htmlspecialchars($recipe['author']) ?> · 
                <?= htmlspecialchars($recipe['created_at']) ?>
            </p>
            <?php if (!empty($recipe['category'])): ?>
                <p><strong>Category:</strong> <?= htmlspecialchars($recipe['category']) ?></p>
            <?php endif; ?>
            <?php if (!empty($recipe['description'])): ?>
                <p><?= nl2br(htmlspecialchars(substr($recipe['description'], 0, 150))) ?>...</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
