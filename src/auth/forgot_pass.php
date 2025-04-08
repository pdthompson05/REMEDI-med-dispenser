<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__.'/../config/db.php';
global $conn;

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
            <body>
                <div class="reset-container">
                    <h1>Reset Password</h1>
                    <form method="POST" action="process_new_pass.php">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        <input type="password" name="new_password" required>
                        <button type="submit">Reset Password</button>
                    </form>
                    <p>Enter your new password below. It must be at least 8 characters long.</p>
                    <p>After resetting, you can use your new password to log in.</p>
                    <p><a href="login.php">Back to Login</a></p>
                    <p>Didn't request a password reset? <a href="register.php">Register</a> or <a href="login.php">Login</a>.</p>
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