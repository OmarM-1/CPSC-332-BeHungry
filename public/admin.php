<?php
require_once 'config/db.php';

// 1. Security Guard: Check if user is Admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die("Access Denied: Admins only.");
}

// 2. Handle specific admin actions (like deleting a user)
if (isset($_GET['delete_user'])) {
    $uid = $_GET['delete_user'];
    // Prevent self-deletion
    if ($uid != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$uid]);
        header("Location: admin.php?msg=user_deleted");
        exit;
    }
}

// 3. Fetch All Data

// Get all recipes (Corrected column names: recipe_id, user_id)
$stmt_recipes = $pdo->query("
    SELECT r.recipe_id, r.title, r.created_at, u.name as author 
    FROM recipes r 
    JOIN users u ON r.user_id = u.user_id 
    ORDER BY r.created_at DESC
");
$all_recipes = $stmt_recipes->fetchAll();

// Get all users (Corrected column names: user_id)
$stmt_users = $pdo->query("SELECT user_id, name, email, created_at FROM users ORDER BY created_at DESC");
$all_users = $stmt_users->fetchAll();

// Get all comments (New for FR-10)
$stmt_comments = $pdo->query("
    SELECT c.comment_id, c.comment_text, c.created_at, u.name as author, r.title as recipe_title, r.recipe_id
    FROM comments c
    JOIN users u ON c.user_id = u.user_id
    JOIN recipes r ON c.recipe_id = r.recipe_id
    ORDER BY c.created_at DESC
");
$all_comments = $stmt_comments->fetchAll();

include 'includes/header.php';
?>

<div class="container" style="max-width: 1000px;">
    <h2>Admin Dashboard</h2>
    <p>Welcome, Admin <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>.</p>

    <?php if (isset($_GET['msg'])): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
            Action completed successfully.
        </div>
    <?php endif; ?>

    <!-- 1. Manage Recipes -->
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
                        <a href="view_recipe.php?id=<?= $r['recipe_id'] ?>"><?= htmlspecialchars($r['title']) ?></a>
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($r['author']) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">
                        <a href="edit_recipe.php?id=<?= $r['recipe_id'] ?>" style="color: blue; margin-right: 10px;">Edit</a>
                        <a href="delete_recipe.php?id=<?= $r['recipe_id'] ?>" 
                           onclick="return confirm('Are you sure you want to delete this recipe?')"
                           style="color: red;">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- 2. Manage Comments (New for FR-10) -->
    <div class="admin-section" style="margin-top: 40px;">
        <h3>Manage Comments</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead style="background: #f4f4f4;">
                <tr>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Comment</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Author</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">On Recipe</th>
                    <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($all_comments)): ?>
                    <tr><td colspan="4" style="padding: 10px;">No comments found.</td></tr>
                <?php else: ?>
                    <?php foreach ($all_comments as $c): ?>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; max-width: 300px;">
                            <?= htmlspecialchars(substr($c['comment_text'], 0, 50)) . (strlen($c['comment_text']) > 50 ? '...' : '') ?>
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($c['author']) ?></td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">
                            <a href="view_recipe.php?id=<?= $c['recipe_id'] ?>"><?= htmlspecialchars($c['recipe_title']) ?></a>
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">
                            <a href="delete_comment.php?id=<?= $c['comment_id'] ?>" 
                               onclick="return confirm('Delete this comment?')"
                               style="color: red;">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 3. Manage Users -->
    <div class="admin-section" style="margin-top: 40px;">
        <h3>Manage Users</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead style="background: #f4f4f4;">
                <tr>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Name</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Email</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Joined</th>
                    <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_users as $u): ?>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($u['name']) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= htmlspecialchars($u['email']) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">
                        <a href="admin.php?delete_user=<?= $u['user_id'] ?>" 
                           onclick="return confirm('Delete this user? This will delete all their recipes and comments.')"
                           style="color: red;">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
