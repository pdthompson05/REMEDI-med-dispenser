<?php
session_start();
require_once "db.php";

if (!isset($_GET['token'])) {
    die("No token provided.");
}

$token = $_GET['token'];
$token_hash = hash('sha256', $token);

global $conn;

# Check token validity
$sql = "SELECT user_id FROM user WHERE verification_token = ? AND is_verified = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    # VERIFY USER
    $sql_update = "UPDATE user SET is_verified = 1, verification_token = NULL WHERE user_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $user_id);

    if ($stmt_update->execute()) {
        echo "Email verified successfully.";
    } else {
        echo "Error verifying email.";
    }
    $stmt_update->close();
} else {
    echo "Error verifying email.";
}

$conn->close();
?>
