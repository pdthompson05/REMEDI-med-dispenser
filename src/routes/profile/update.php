<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require_once __DIR__.'/../../config/db.php';
global $conn;

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$date_of_birth = ! empty($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : null;
$caretaker_name = ! empty($_POST['caretaker_name']) ? trim($_POST['caretaker_name']) : null;
$caretaker_email = ! empty($_POST['caretaker_email']) ? trim($_POST['caretaker_email']) : null;

if (empty($first_name) || empty($last_name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
    exit;
}

$sql_check_email = 'SELECT user_id FROM user WHERE email = ? AND user_id != ?';
$stmt_check = $conn->prepare($sql_check_email);
$stmt_check->bind_param('si', $email, $user_id);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email is already in use']);
    exit;
}
$stmt_check->close();

$profile_picture_path = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__.'/uploads/profile_pictures/';
    if (! is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_name = basename($_FILES['profile_picture']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

    if (! in_array($file_ext, $allowed_exts)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
        exit;
    }

    $new_file_name = 'profile_'.$user_id.'_'.time().'.'.$file_ext;
    $file_path = $upload_dir.$new_file_name;

    if (move_uploaded_file($file_tmp, $file_path)) {
        $profile_picture_path = 'uploads/profile_pictures/'.$new_file_name;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload profile picture.']);
        exit;
    }
}

$sql_update_user = 'UPDATE user SET email = ? WHERE user_id = ?';
$stmt_user = $conn->prepare($sql_update_user);
$stmt_user->bind_param('si', $email, $user_id);
$stmt_user->execute();
$stmt_user->close();

$sql_update_profile = 'UPDATE user_profile SET first_name = ?, last_name = ?, date_of_birth = ?, caretaker_name = ?, caretaker_email = ?, profile_picture = IFNULL(?, profile_picture) WHERE user_id = ?';
$stmt_profile = $conn->prepare($sql_update_profile);
$stmt_profile->bind_param('ssssssi', $first_name, $last_name, $date_of_birth, $caretaker_name, $caretaker_email, $profile_picture_path, $user_id);

if ($stmt_profile->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'SQL Error: '.$stmt_profile->error]);
}

$stmt_profile->close();
$conn->close();
