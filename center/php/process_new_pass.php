<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug incoming data
var_dump($_POST);


session_start();
require_once "db.php"; // DB connection



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate all required fields are present
    if (!isset($_POST['new_password']) || !isset($_POST['token'])) {
        die("Missing required fields.");
    }

    // Get and validate token
    $token = trim($_POST['token']);
    
    if (empty($token)) {
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
    
    // Get and validate password
    $new_password = $_POST['new_password'];
    
    if (strlen($new_password) < 8) {
        die("Password must be at least 8 characters long.");
    }
    
    // Hash and update password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    $sql = "UPDATE user SET password_hash = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $password_hash, $user["id"]);
    
    if ($stmt->execute()) {
        echo "Password updated successfully. You can now login.";
    } else {
        die("Error updating password.");
    }
} else {
    die("Invalid request method.");
}

?>