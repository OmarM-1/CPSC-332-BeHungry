<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $steps       = trim($_POST['steps'] ?? '');
    $category    = trim($_POST['category'] ?? '');

    if ($title === '' || $ingredients === '' || $steps === '') {
        $errors[] = "Title, ingredients, and steps are required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO recipes (user_id, title, description, ingredients, steps, category)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $description,
            $ingredients,
            $steps,
            $category
        ]);
        header("Location: index.php");
        exit;
    }
}

include '../includes/header.php';
?>

<h2>Submit a Recipe</h2>

<?php if ($errors): ?>
    <div style="color:red;">
        <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post">
    <label>Title</label>
    <input type="text" name="title" required>

    <label>Description (short summary)</label>
    <textarea name="description" rows="3"></textarea>

    <label>Ingredients (one per line or comma-separated)</label>
    <textarea name="ingredients" rows="5" required></textarea>

    <label>Steps</label>
    <textarea name="steps" rows="7" required></textarea>

    <label>Category (e.g., breakfast, vegan, dessert)</label>
    <input type="text" name="category">

    <button type="submit">Post Recipe</button>
</form>

<?php include '../includes/footer.php'; ?>
