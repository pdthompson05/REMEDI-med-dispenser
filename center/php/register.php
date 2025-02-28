<?php
session_start();
header("Content-Type: application/json");
require_once "db.php";
global $conn;

$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

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

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$sql_insert_user = "INSERT INTO user (email, password_hash, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
$stmt_user = $conn->prepare($sql_insert_user);
$stmt_user->bind_param("ss", $email, $hashed_password);

if ($stmt_user->execute()) {
    $user_id = $stmt_user->insert_id;
    $stmt_user->close();

    $sql_insert_profile = "INSERT INTO user_profile (user_id, first_name, last_name) VALUES (?, ?, ?)";
    $stmt_profile = $conn->prepare($sql_insert_profile);
    $stmt_profile->bind_param("iss", $user_id, $first_name, $last_name);

    if ($stmt_profile->execute()) {
        echo json_encode(["status" => "success", "message" => "Registration successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Profile creation failed"]);
    }
    $stmt_profile->close();
} else {
    echo json_encode(["status" => "error", "message" => "Registration error"]);
}


$password_hash = password_hash($password, PASSWORD_DEFAULT);
$verification_token = bin2hex(random_bytes(16));
$verification_token_hash = hash('sha256', $verification_token);

$sql = "INSERT INTO user (email, password_hash, created_at, updated_at, verification_token, is_verified) VALUES (?, ?, NOW(), NOW(), ?, 0)";
$stmt_user = $conn->prepare($sql);
$stmt_user->bind_param("sss", $email, $password_hash, $verification_token_hash);


if ($stmt_user->execute()) {
    // Send verification email
    $verification_link = "http://yourdomain.com/verify.php?token=" . $verification_token; // Adjust this URL
    $subject = "Email Verification";
    $message = "Please click the following link to verify your email: " . $verification_link;
    $headers = "From: no-reply@yourdomain.com"; // Update with your email

    if (mail($email, $subject, $message, $headers)) {
        echo json_encode(["status" => "success", "message" => "Registration successful. Please check your email for verification."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration successful, but failed to send verification email."]);
    }
} else {
    error_log("SQL Error: " . $stmt_user->error);
    echo json_encode(["status" => "error", "message" => "Failed to register user. Please try again later."]);
}

$stmt->close();
$conn->close();
?>