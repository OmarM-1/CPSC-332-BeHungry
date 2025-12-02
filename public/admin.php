<?php
require_once 'config/db.php';

// 1. Security Guard: Check if user is Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die("Access Denied: Admins only.");
}

// 2. Handle specific admin actions (like deleting a user)
if (isset($_GET['delete_user'])) {
    $uid = $_GET['delete_user'];
    // Delete user logic here (omitted for brevity, similar to recipe delete)
}

// 3. Fetch All Data
// Get all recipes with author names
$stmt_recipes = $pdo->query("
    SELECT r.id, r.title, r.created_at, u.name as author 
    FROM recipes r 
    JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC
");
$all_recipes = $stmt_recipes->fetchAll();

// Get all users
$stmt_users = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
$all_users = $stmt_users->fetchAll();

include 'includes/header.php';
?>

<div class="container" style="max-width: 1000px;">
    <h2>Admin Dashboard</h2>
    <p>Welcome, Admin <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>.</p>

    <div class="admin-section" style="margin-top: 30px;">
        <h3>Manage Recipes</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead style="background: #f4f4f4;">
                <tr>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Title</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Author</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Date</th>
                    <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_recipes as $r): ?>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <a href="view_recipe.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></a>
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($r['author']) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">
                        <a href="edit_recipe.php?id=<?= $r['id'] ?>" style="color: blue; margin-right: 10px;">Edit</a>
                        <a href="delete_recipe.php?id=<?= $r['id'] ?>" 
                           onclick="return confirm('Are you sure you want to delete this recipe? This cannot be undone.');"
                           style="color: red;">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-section" style="margin-top: 40px;">
        <h3>User Overview</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead style="background: #f4f4f4;">
                <tr>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Name</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Email</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_users as $u): ?>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($u['name']) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($u['email']) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>