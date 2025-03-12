<?php
session_start();
require_once "db.php"; // Ensure database connection

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
    $token_hash = hash('sha256', $token);

    // Verify token
    $sql = "SELECT * FROM user WHERE reset_token_hash = ? AND reset_token_expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['reset_token'] = $token_hash; // Store valid token in session
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Your Password</title>
        </head>
        <body>
            <h1>Reset Your Password</h1>
            <form method="POST">
                <label for="password">New Password:</label>
                <input type="password" name="password" id="password" required minlength="8">
                <br>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                <br>
                <button type="submit">Reset Password</button>
            </form>
        </body>
        </html>
        <?php
    } else {
        echo "Invalid or expired token.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['reset_token'])) {
        echo "Session expired. Please request a new reset link.";
        exit;
    }

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters long.";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update password in database
    $sql = "UPDATE user SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE reset_token_hash = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed_password, $_SESSION['reset_token']);
    
    if ($stmt->execute()) {
        unset($_SESSION['reset_token']); // Remove token from session
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Password Reset Successful</title>
        </head>
        <body>
            <h1>Thank You for Resetting Your Password</h1>
            <p>Your password has been successfully reset.</p>
        </body>
        </html>
        <?php
    } else {
        echo "Something went wrong. Please try again.";
    }
} else {
    echo "Invalid request.";
}
?>
