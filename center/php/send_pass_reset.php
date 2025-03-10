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

if ($stmt->affected_rows > 0) {
    require_once "verify_mail.php"; // Ensure the path is correct

    $mail->setFrom("noreply.remedi@gmail.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END
    Click <a href="https://section-three.it313communityprojects.website/reset_pass.php?token=$token">here</a>
    to reset your password
    END;

    try {
        $mail->send();
        echo "Message sent, please check your inbox";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "No user found with that email address.";
}

$stmt->close();
$conn->close();


?>