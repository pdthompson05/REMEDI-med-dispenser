
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
            <body>
                <div class="reset-container">
                    <h1>Reset Password</h1>
                    <form method="post" action="process_new_pass.php">

                        <input type="hidden" name="token" value="<?php htmlspecialchars($token); ?>">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password" required>
                        <button type="submit">Password</button>
                    </form>
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