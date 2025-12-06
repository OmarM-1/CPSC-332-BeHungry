<?php
require_once '../config/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_id = trim($_POST['login_id'] ?? ''); // Accepts Email OR Phone
    $password = $_POST['password'] ?? '';

    if ($login_id === '' || $password === '') {
        $errors[] = "Email/Phone and password are required.";
    } else {
        // Check for Email OR Phone match
        $stmt = $pdo->prepare("SELECT user_id, name, password_hash, is_admin FROM users WHERE email = ? OR phone = ?");
        $stmt->execute([$login_id, $login_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login Success
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['name']     = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin']; // Set admin status if available
            
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Invalid credentials.";
        }
    }
}

include '../includes/header.php';
?>

<div class="form-container" style="max-width: 400px; margin: 2rem auto;">
    <h2>Login</h2>

    <?php if (isset($_GET['registered'])): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            Registration successful. Please log in.
        </div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <?php foreach ($errors as $e): ?>
                <p style="margin: 0;"><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Email or Phone Number</label>
        <input type="text" name="login_id" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

        <label>Password</label>
        <input type="password" name="password" required style="width: 100%; padding: 8px; margin-bottom: 20px;">

        <button type="submit" style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Login</button>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <p>Or login with</p>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <a href="social_login.php?provider=google" style="padding: 10px 20px; background: #db4437; color: white; text-decoration: none; border-radius: 4px;">Google</a>
            <a href="social_login.php?provider=facebook" style="padding: 10px 20px; background: #3b5998; color: white; text-decoration: none; border-radius: 4px;">Facebook</a>
        </div>
    </div>
    
    <p style="text-align: center; margin-top: 15px;">
        Don't have an account? <a href="register.php">Register here</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>
