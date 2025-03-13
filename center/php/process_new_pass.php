<?php
session_start();
require_once "db.php"; // DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password']) && isset($_POST['token'])) {
    // Get token from URL parameter
    $token = $_POST['token'];
    
    if (!isset($token)) {
        die("No token provided.");
    }
    
    $token_hash = hash('sha256', $token);

    // Check if the token is valid
    $sql = "SELECT * FROM user WHERE reset_token_hash = ? AND reset_token_expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("Token invalid or expired.");
    }
    
    $user = $result->fetch_assoc();
    
    // Validate password - add your own validation rules
    if (strlen($_POST['password']) < 8) {
        die("Password must be at least 8 characters long.");
    }
    
    // Hash and update password
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $sql = "UPDATE user SET password_hash = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $password_hash, $user["id"]);
    $stmt->execute();
    
    echo "Password updated. You can now login.";
} else {
    echo "Please submit the form with a new password.";
}
?>