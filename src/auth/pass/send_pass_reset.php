<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__.'/../../../config/db.php';
require_once 'mail.php';
global $conn;

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

$token = bin2hex(random_bytes(16));
$token_hash = hash('sha256', $token);
$expiry = date('Y-m-d H:i:s', time() + 60 * 30);

// Update the user's reset token in the database
$sql = 'UPDATE user SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $token_hash, $expiry, $email);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Call the sendPasswordResetEmail function
    if (sendPasswordResetEmail($email, $token)) {
        echo 'A password reset email has been sent to your email address. Please check your inbox.';
        echo '<br><a href="/frontend/html/login.html" class="button">Click here after you have reset your password</a>';
    } else {
        echo 'Failed to send the password reset email. Please try again.';
    }
} else {
    echo 'No user found with that email address.';
}
