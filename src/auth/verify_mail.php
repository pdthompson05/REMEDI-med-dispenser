<?php
require __DIR__. '/../PHPMailer/src/PHPMailer.php';
require __DIR__. '/../PHPMailer/src/SMTP.php';
require __DIR__. '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../config/env.php'; // loadEnv

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

loadEnv(__DIR__ . '/../../.env'); // loadEnv for .env file

function sendVerificationEmail($email, $token) {
    $mailUser = getenv('MAIL_USER');
    $mailPass = getenv('MAIL_PASS');

    if (!$mailUser || !$mailPass) {
        die("Error: Check .env file.\n");
    }

    $mail = new PHPMailer(true);
        
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailUser;
        $mail->Password   = $mailPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($mailUser, 'noreply-remedi');
        $mail->addAddress($email);

        $verification_link = "https://section-three.it313communityprojects.website/src/auth/verify_token.php?token=" . urlencode($token);
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email';
        $mail->Body    = "<p>Click the link below to verify your email:</p>
                          <p><a href='$verification_link'>$verification_link</a></p>";

        return $mail->send();
    } catch (Exception $e) {
        return false;
        
    }
}


# Function for the rest password

function sendPasswordResetEmail($email, $token) {
    $mailUser = getenv('MAIL_USER');
    $mailPass = getenv('MAIL_PASS');

    if (!$mailUser || !$mailPass) {
        die("Error: Check .env file.\n");
    }

    $mail = new PHPMailer(true);
        
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailUser;
        $mail->Password   = $mailPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($mailUser, 'noreply-remedi');
        $mail->addAddress($email);

        $reset_link = "https://section-three.it313communityprojects.website/src/auth/forgot_pass.php?token=" . urlencode($token);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "<p>Click the link below to reset your password:</p>
                          <p><a href='$reset_link'>Reset Password</a></p>";

        return $mail->send(); // Return the result of the send operation
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false; // Return false on error
    }
}
?>

