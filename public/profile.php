<?php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$profile_id = $_GET['id'] ?? $_SESSION['user_id'];
$is_own_profile = ($profile_id == $_SESSION['user_id']);

$stmt = $pdo->prepare("SELECT id, name, email, bio, profile_image_url, created_at FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.*, COUNT(l.id) as like_count
    FROM recipes r
    LEFT JOIN likes l ON r.id = l.recipe_id
    WHERE r.user_id = ?
    GROUP BY r.id
    ORDER BY r.created_at DESC
");
$stmt->execute([$profile_id]);
$user_recipes = $stmt->fetchAll();

$liked_recipes = [];
if ($is_own_profile) {
    $stmt = $pdo->prepare("
        SELECT r.*, u.name as author
        FROM recipes r
        JOIN likes l ON r.id = l.recipe_id
        JOIN users u ON r.user_id = u.id
        WHERE l.user_id = ?
        ORDER BY l.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $liked_recipes = $stmt->fetchAll();
}

include 'includes/header.php';
?>

<div class="profile-header">
    <div class="profile-info">
        <div class="profile-avatar">
            <?php if ($user['profile_image_url']): ?>
                <img src="<?= htmlspecialchars($user['profile_image_url']) ?>" alt="Profile">
            <?php else: ?>
                <div class="avatar-placeholder"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
            <?php endif; ?>
        </div>
        <div class="profile-details">
            <h1><?= htmlspecialchars($user['name']) ?></h1>
            <p class="join-date">Member since <?= date('F Y', strtotime($user['created_at'])) ?></p>
            <?php if ($user['bio']): ?>
                <p class="profile-bio"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
            <?php endif; ?>
            
            <?php if ($is_own_profile): ?>
                <a href="edit_profile.php" class="edit-profile-btn">Edit Profile</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="profile-stats">
        <div class="stat">
            <span class="stat-number"><?= count($user_recipes) ?></span>
            <span class="stat-label">Recipes</span>
        </div>
        <div class="stat">
            <span class="stat-number">
                <?php 
                $total_likes = 0;
                foreach ($user_recipes as $recipe) {
                    $total_likes += $recipe['like_count'];
                }
                echo $total_likes;
                ?>
            </span>
            <span class="stat-label">Total Likes</span>
        </div>
    </div>
</div>

<div class="profile-tabs">
    <button class="tab-btn active" onclick="showTab('my-recipes')">My Recipes</button>
    <?php if ($is_own_profile): ?>
        <button class="tab-btn" onclick="showTab('liked-recipes')">Liked Recipes</button>
    <?php endif; ?>
</div>

<div id="my-recipes" class="tab-content active">
    <h2><?= $is_own_profile ? 'My Recipes' : 'Recipes by ' . htmlspecialchars($user['name']) ?></h2>
    
    <?php if (empty($user_recipes)): ?>
        <p class="empty-message">No recipes yet.</p>
    <?php else: ?>
        <div class="recipes-grid">
            <?php foreach ($user_recipes as $recipe): ?>
                <div class="recipe-card">
                    <h3>
                        <a href="view_recipe.php?id=<?= $recipe['id'] ?>">
                            <?= htmlspecialchars($recipe['title']) ?>
                        </a>
                    </h3>
                    <p class="recipe-meta">
                        <?= date('M j, Y', strtotime($recipe['created_at'])) ?>
                        <span class="like-count"><?= $recipe['like_count'] ?></span>
                    </p>
                    <?php if ($recipe['description']): ?>
                        <p><?= nl2br(htmlspecialchars(substr($recipe['description'], 0, 100))) ?>...</p>
                    <?php endif; ?>
                    <?php if ($is_own_profile): ?>
                        <div class="recipe-actions">
                            <a href="edit_recipe.php?id=<?= $recipe['id'] ?>" class="edit-btn">Edit</a>
                            <a href="delete_recipe.php?id=<?= $recipe['id'] ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Delete this recipe?')">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($is_own_profile): ?>
<div id="liked-recipes" class="tab-content">
    <h2>My Liked Recipes</h2>
    
    <?php if (empty($liked_recipes)): ?>
        <p class="empty-message">You haven't liked any recipes yet.</p>
    <?php else: ?>
        <div class="recipes-grid">
            <?php foreach ($liked_recipes as $recipe): ?>
                <div class="recipe-card">
                    <h3>
                        <a href="view_recipe.php?id=<?= $recipe['id'] ?>">
                            <?= htmlspecialchars($recipe['title']) ?>
                        </a>
                    </h3>
                    <p class="recipe-meta">
                        By <?= htmlspecialchars($recipe['author']) ?> · 
                        <?= date('M j, Y', strtotime($recipe['created_at'])) ?>
                    </p>
                    <?php if ($recipe['description']): ?>
                        <p><?= nl2br(htmlspecialchars(substr($recipe['description'], 0, 100))) ?>...</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.getElementById(tabName).classList.add('active');
    
    event.currentTarget.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>