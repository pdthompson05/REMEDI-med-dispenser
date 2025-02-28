<?php
session_start();
require_once "db.php";
global $conn;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Hash the token for comparison
    $token_hash = hash('sha256', $token);

    // Update the user to set is_verified to 1 where verification_token matches
    $sql_verify = "UPDATE user SET is_verified = 1 WHERE verification_token = ?";
    $stmt = $conn->prepare($sql_verify);
    $stmt->bind_param("s", $token_hash);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "Email verified successfully!";
    } else {
        echo "Invalid or expired token.";
    }
    $stmt->close();
} else {
    echo "No token provided.";
}

$conn->close();
?>