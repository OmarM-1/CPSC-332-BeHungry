<?php
require_once 'config/db.php';

// 1. Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Get Recipe ID
$recipe_id = $_GET['id'] ?? null;
if (!$recipe_id) {
    header("Location: profile.php");
    exit;
}

// 3. Fetch existing recipe to verify ownership and pre-fill form
$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch();

// Check if recipe exists
if (!$recipe) {
    die("Recipe not found.");
}

// Check Ownership (Critical Security Step)
if ($recipe['user_id'] != $_SESSION['user_id']) {
    die("Access Denied: You are not the author of this recipe.");
}

$errors = [];

// 4. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $steps       = trim($_POST['steps'] ?? '');
    $category    = trim($_POST['category'] ?? '');

    // Validation
    if ($title === '' || $ingredients === '' || $steps === '') {
        $errors[] = "Title, ingredients, and steps are required.";
    }

    if (empty($errors)) {
        // Update Query
        $stmt = $pdo->prepare("
            UPDATE recipes 
            SET title = ?, description = ?, ingredients = ?, steps = ?, category = ? 
            WHERE id = ? AND user_id = ?
        ");
        
        $result = $stmt->execute([
            $title, 
            $description, 
            $ingredients, 
            $steps, 
            $category, 
            $recipe_id, 
            $_SESSION['user_id']
        ]);

        if ($result) {
            header("Location: view_recipe.php?id=" . $recipe_id . "&updated=1");
            exit;
        } else {
            $errors[] = "Failed to update recipe.";
        }
    }
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 800px;">
    <h2>Edit Recipe: <?= htmlspecialchars($recipe['title']) ?></h2>

    <?php if ($errors): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <?php foreach ($errors as $e): ?>
                <p style="margin: 0;"><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($recipe['title']) ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

        <label>Category</label>
        <input type="text" name="category" value="<?= htmlspecialchars($recipe['category']) ?>" placeholder="e.g. Breakfast, Vegan" style="width: 100%; padding: 8px; margin-bottom: 10px;">

        <label>Description (Short summary)</label>
        <textarea name="description" rows="3" style="width: 100%; padding: 8px; margin-bottom: 10px;"><?= htmlspecialchars($recipe['description']) ?></textarea>

        <label>Ingredients</label>
        <textarea name="ingredients" rows="6" required style="width: 100%; padding: 8px; margin-bottom: 10px;"><?= htmlspecialchars($recipe['ingredients']) ?></textarea>
        <small style="display:block; margin-bottom:10px; color:#666;">Tip: List each ingredient on a new line.</small>

        <label>Steps / Instructions</label>
        <textarea name="steps" rows="8" required style="width: 100%; padding: 8px; margin-bottom: 20px;"><?= htmlspecialchars($recipe['steps']) ?></textarea>

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Update Recipe</button>
            <a href="view_recipe.php?id=<?= $recipe_id ?>" style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>