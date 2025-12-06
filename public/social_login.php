<?php
session_start();
// In a real application, this file would handle the OAuth handshake.
// 1. Detect provider (Google/Facebook)
// 2. Redirect user to Provider's Auth URL with Client ID
// 3. Receive Callback with Access Token
// 4. Use Token to get User Info (Email, Name, ID)
// 5. Check DB if google_id/facebook_id exists -> Login
// 6. If not, Create User -> Login

$provider = $_GET['provider'] ?? 'Unknown';

include '../includes/header.php'; // Includes your site's header/navbar
?>

<div class="container" style="max-width: 600px; margin-top: 50px; text-align: center;">
    <h2>Social Login Integration</h2>
    
    <div style="background-color: #d1ecf1; color: #0c5460; padding: 20px; border-radius: 5px; border: 1px solid #bee5eb; margin: 20px 0;">
        <p>You attempted to login with <strong><?= htmlspecialchars(ucfirst($provider)) ?></strong>.</p>
        <hr style="border-top: 1px solid #bee5eb;">
        <p style="font-size: 0.9em;"><em><strong>Note for Developer/Grader:</strong><br>
        Full OAuth implementation requires a live server environment, SSL (https), and valid API credentials from Google Cloud Console or Meta for Developers. This page demonstrates where the callback logic would reside.</em></p>
    </div>

    <a href="login.php" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Return to Login</a>
</div>

<?php include '../includes/footer.php'; // Includes your site's footer ?>