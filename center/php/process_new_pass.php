<?php
session_start();
require_once "db.php"; // DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    $token_hash = hash('sha256', $token);

    // Check if the token is valid
    $sql = "SELECT * FROM user WHERE reset_token_hash = ? AND reset_token_expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Token is valid, process the new password
        if (isset($_POST['new_password'])) {
            $new_password = $_POST['new_password'];

            // Validate the new password
            if (strlen($new_password) < 6) {
                echo "Password must be at least 6 characters.";
                exit;
            }

            // Update the password in the database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE user SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE reset_token_hash = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $hashed_password, $token_hash);
            $update_stmt->execute();

            if ($update_stmt->affected_rows === 1) {
                echo "Password has been reset successfully.";
            } else {
                echo "Failed to reset password.";
            }
        } else {
            // Show the password reset form
            ?>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Reset Password</title>
            </head>
            <body>
                <div class="reset-container">
                    <h1>Reset Password</h1>
                    <form method="post" action="">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password" required>
                        <button type="submit">Reset Password</button>
                    </form>
                </div>
            </body>
            </html>
            <?php
        }
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}
?>