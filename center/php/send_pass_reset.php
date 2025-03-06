<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "db.php"; // DB connection

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

$token = bin2hex(random_bytes(16));

$token_hash = hash('sha256', $token);

$expiry = date("Y-m-d H:i:s", time() + 60 * 30);


$sql = "UPDATE user SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("sss", $token_hash, $expiry, $email);

$stmt->execute();

?>


