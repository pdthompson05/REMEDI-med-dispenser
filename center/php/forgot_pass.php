# This file will handle the password 
# reset process by verifying the token and allowing 
# the user to set a new password

<?php
session_start();
require_once "db.php"; // DB connection

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
    $token_hash = hash('sha256', $token);

    // Check if the token is valid
    $sql = "SELECT * FROM user WHERE reset_token_hash = ? AND reset_token_expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Token is valid, show the password reset form
        ?>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Password Reset Confirmation</title>
        </head>
        <body>
            <div class="reset-container">
                <h1>Thank You for Resetting Your Password</h1>
                <p>Your password has been successfully reset.</p>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}
?>