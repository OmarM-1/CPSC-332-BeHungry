<?php
require_once '../config/db.php';

// 1. Get Filter/Sort Parameters
$sort = $_GET['sort'] ?? 'newest';
$category = $_GET['category'] ?? '';

// 2. Build Query
$sql = "
    SELECT r.id, r.title, r.description, r.category, r.created_at, u.name AS author, 
           COUNT(l.id) as like_count
    FROM recipes r
    JOIN users u ON r.user_id = u.id
    LEFT JOIN likes l ON r.id = l.recipe_id
";

$params = [];

// Apply Category Filter (Diet)
if (!empty($category)) {
    $sql .= " WHERE r.category = ?";
    $params[] = $category;
}

// Group by Recipe ID for Aggregate Count
$sql .= " GROUP BY r.id";

// Apply Sorting
if ($sort === 'popular') {
    // Sort by Like Count DESC, then Newest
    $sql .= " ORDER BY like_count DESC, r.created_at DESC";
} else {
    // Default: Sort by Newest
    $sql .= " ORDER BY r.created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="browse-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Browse Recipes</h2>
    
    <!-- Filter and Sort Controls -->
    <form method="get" class="browse-controls" style="display: flex; gap: 10px;">
        <!-- Category Filter -->
        <select name="category" onchange="this.form.submit()" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="">All Categories</option>
            <?php 
            $cats = ['Breakfast', 'Lunch', 'Dinner', 'Dessert', 'Vegetarian', 'Vegan'];
            foreach ($cats as $c): 
            ?>
                <option value="<?= $c ?>" <?= $category === $c ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Sort Control -->
        <select name="sort" onchange="this.form.submit()" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
            <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Most Popular</option>
        </select>
    </form>
</div>

<!-- Recipe Grid -->
<?php if (!$recipes): ?>
    <div style="text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px;">
        <p>No recipes found matching your criteria.</p>
        <a href="index.php" style="color: #007bff; text-decoration: none;">Clear Filters</a>
    </div>
<?php else: ?>
    <div class="recipes-grid">
        <?php foreach ($recipes as $recipe): ?>
            <div class="recipe-card" style="border: 1px solid #eee; padding: 15px; border-radius: 8px; margin-bottom: 20px; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <h3 style="margin: 0 0 10px 0;">
                        <a href="view_recipe.php?id=<?= $recipe['id'] ?>" style="text-decoration: none; color: #333;">
                            <?= htmlspecialchars($recipe['title']) ?>
                        </a>
                    </h3>
                    <?php if (!empty($recipe['category'])): ?>
                        <span style="font-size: 0.8em; background: #e9ecef; padding: 2px 8px; border-radius: 12px; color: #495057;">
                            <?= htmlspecialchars($recipe['category']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <p style="font-size: 0.9em; color: #666; margin-bottom: 10px;">
                    By <strong><?= htmlspecialchars($recipe['author']) ?></strong> · 
                    <?= htmlspecialchars(date('M j, Y', strtotime($recipe['created_at']))) ?>
                </p>

                <?php if (!empty($recipe['description'])): ?>
                    <p style="margin-bottom: 15px; line-height: 1.5;">
                        <?= nl2br(htmlspecialchars(substr($recipe['description'], 0, 150))) ?>
                        <?= strlen($recipe['description']) > 150 ? '...' : '' ?>
                    </p>
                <?php endif; ?>

                <div style="border-top: 1px solid #f1f1f1; padding-top: 10px; display: flex; align-items: center; gap: 5px; color: #e91e63;">
                    <!-- Simple Heart Icon for Likes -->
                    <span style="font-size: 1.2em;">&hearts;</span> 
                    <strong><?= $recipe['like_count'] ?></strong> Likes
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
