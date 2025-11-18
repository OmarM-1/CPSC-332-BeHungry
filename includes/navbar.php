<?php

$logged_in = isset($_SESSION['user_id']);
?>
<nav style ="display:flex: align-items:center; justify-content:space-between;">
    <div>
        <a href="../index.php">Home</a>
        <?php if ($logged_in): ?>
            <a href="profile.php">Profile</a>
            <a href ="submit_recipe.php">Submit Recipe</a>
            <a href="logout.php">Logout (<?=htmlspecialchars($_SESSION['name'])?>)</a>
        <?php else; ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
    <form action="search.php" method="get" style="margin:0;">
        <input type="text" name="q" placeholder="Search recipes..." style="padding:0.2rem 0.4rem;">
        <button type="submit">Search</button>
    </form>
</nav>

        