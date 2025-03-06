<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

require_once "db.php"; // DB connection
require_once "verify_mail.php"; // Email verification

global $conn;

$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$date_of_birth = (!empty($_POST['date_of_birth'])) ? trim($_POST['date_of_birth']) : NULL;

if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Fields required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email"]);
    exit;
}

$sql_check = "SELECT user_id FROM user WHERE email = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already registered"]);
    exit;
}
$stmt->close();

$password_hash = password_hash($password, PASSWORD_BCRYPT);
$verification_token = bin2hex(random_bytes(16));


$sql_insert_user = "INSERT INTO user (email, password_hash, created_at, updated_at, verification_token, is_verified) VALUES (?, ?, NOW(), NOW(), ?, 0)";
$stmt_user = $conn->prepare($sql_insert_user);
$stmt_user->bind_param("sss", $email, $password_hash, $verification_token);

error_log("Inserting user: email=$email, password_hash=$password_hash, verification_token=$verification_token");

if ($stmt_user->execute()) {
    // Send verification email
    $verification_link = "http://section-three.it313communityprojects.website/center/php/verify.php?token=" . $verification_token;
    $subject = "Email Verification";
    $message = "Please click the following link to verify your email: " . $verification_link;
    $headers = "From: Jasonamaya6@icloud.com"; // Update with your email

    if (mail($email, $subject, $message, $headers)) {
        echo json_encode(["status" => "success", "message" => "Registration successful. Please check your email for verification."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration successful, but failed to send verification email."]);
    }
} else {
    error_log("SQL Error: " . $stmt_user->error);
    echo json_encode(["status" => "error", "message" => "Failed to register user. Please try again later."]);
}

$stmt_user->close();
$conn->close();
?>