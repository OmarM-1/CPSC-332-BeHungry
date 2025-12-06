<?php
require_once '../config/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Basic Validation
    if ($name === '') {
        $errors[] = "Name is required.";
    }
    
    // Require EITHER Email OR Phone
    if ($email === '' && $phone === '') {
        $errors[] = "Please provide either an Email address or a Phone number.";
    }

    if ($password === '' || $confirm === '') {
        $errors[] = "Password fields are required.";
    }
    
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Database Checks
    if (empty($errors)) {
        // Check Email
        if ($email !== '') {
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Email is already registered.";
            }
        }
        
        // Check Phone
        if ($phone !== '') {
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE phone = ?");
            $stmt->execute([$phone]);
            if ($stmt->fetch()) {
                $errors[] = "Phone number is already registered.";
            }
        }

        // Create User
        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Handle empty strings for NULL database fields
            $emailParams = $email === '' ? null : $email;
            $phoneParams = $phone === '' ? null : $phone;

            $stmt = $pdo->prepare("INSERT INTO users (name, username, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)");
            // Using email as username default if not provided, or logic to generate one
            $username = strtolower(str_replace(' ', '', $name)) . rand(100,999); 
            
            try {
                $stmt->execute([$name, $username, $emailParams, $phoneParams, $hash]);
                header("Location: login.php?registered=1");
                exit;
            } catch (PDOException $e) {
                $errors[] = "Registration failed: " . $e->getMessage();
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="form-container" style="max-width: 400px; margin: 2rem auto;">
    <h2>Create an Account</h2>

    <?php if ($errors): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <?php foreach ($errors as $e): ?>
                <p style="margin: 0;"><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Display Name</label>
        <input type="text" name="name" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

        <label>Email (Optional)</label>
        <input type="email" name="email" style="width: 100%; padding: 8px; margin-bottom: 10px;">

        <label>Phone Number (Optional)</label>
        <input type="tel" name="phone" placeholder="e.g. 1234567890" style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <small style="display:block; margin-bottom: 10px; color: #666;">Enter at least one contact method.</small>

        <label>Password</label>
        <input type="password" name="password" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required style="width: 100%; padding: 8px; margin-bottom: 20px;">

        <button type="submit" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Register</button>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <p>Or sign up with</p>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <a href="social_login.php?provider=google" style="padding: 10px 20px; background: #db4437; color: white; text-decoration: none; border-radius: 4px;">Google</a>
            <a href="social_login.php?provider=facebook" style="padding: 10px 20px; background: #3b5998; color: white; text-decoration: none; border-radius: 4px;">Facebook</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
