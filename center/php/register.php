<?php
session_start();
header("Content-Type: application/json");
require_once "db.php";
global $conn;

$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';
$account_type = isset($_POST['account_type']) ? trim($_POST['account_type']) : '';

if (empty($first_name) || empty($last_name) ||empty($email) || empty($password) || empty($dob) || empty($account_type)) {
    echo json_encode(["status" => "error", "message" => "Fields required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email"]);
    exit;
}

$allowed_types = ['patient', 'caregiver'];
if (!in_array($account_type, $allowed_types)) {
    echo json_encode(["status" => "error", "message" => "Invalid account type"]);
    exit;
}

$sql_check = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("s", $email);
$stmt->execute();
//$result = $stmt->get_result();
$stmt->store_result(); // this allows row count checks

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already registered"]);
    exit;
}
$stmt->close();

$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$verification_token = bin2hex(random_bytes(50));

$sql_insert = "INSERT INTO users (first_name, last_name, email, password_hash, date_of_birth, account_type, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("ssssss", $first_name, $last_name, $email, $hashed_password, $dob, $account_type);

if ($stmt_insert->execute()) {
    echo json_encode(["status" => "success", "message" => "Registration successful"]);
} else {
    echo json_encode(["status" => "error", "message" => "Registration failed"]);
}

$stmt_insert->close();
$conn->close();