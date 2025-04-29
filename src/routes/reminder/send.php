<?php

require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../config/env.php';
require_once __DIR__.'/../../../PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/../../../PHPMailer/src/SMTP.php';
require_once __DIR__.'/../../../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

loadEnv(__DIR__.'/../../../.env'); // loadEnv for .env file

$now = new DateTime;
$upcoming = clone $now;
$upcoming->modify('+10 minutes');
$now_str = $now->format('Y-m-d H:i:s');
$upcoming_str = $upcoming->format('Y-m-d H:i:s');

$mailUser = getenv('MAIL_USER');
$mailPass = getenv('MAIL_PASS');

// Fetch reminders due in next 10 minutes
$sql = '
  SELECT u.email, m.med_name, c.event_datetime
  FROM calendar_events c
  JOIN user u ON c.user_id = u.user_id
  JOIN med m ON c.med_id = m.med_id
  WHERE c.event_datetime BETWEEN ? AND ?
';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $now_str, $upcoming_str);
$stmt->execute();
$result = $stmt->get_result();

// Send reminders
while ($row = $result->fetch_assoc()) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $mailUser;
        $mail->Password = $mailPass;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($mailUser, 'REMEDI Reminder');
        $mail->addAddress($row['email']);
        $mail->Subject = 'Medication Reminder';
        $mail->Body = "It's time to take your medication: ".$row['med_name'].
                      "\nScheduled time: ".$row['event_datetime'];

        $mail->send();
    } catch (Exception $e) {
        error_log("Failed to send reminder to {$row['email']}: ".$mail->ErrorInfo);
    }
}
