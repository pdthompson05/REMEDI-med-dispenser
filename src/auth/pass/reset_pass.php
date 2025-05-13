<?php
// reset_pass.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Password | REMEDI</title>
  <link rel="stylesheet" href="../../frontend/html/css/global.css" />
  <link rel="stylesheet" href="../../frontend/html/css/login.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body class="login-page">
  <div class="login-card">
    <div class="login-header">
      <img src="../../frontend/html/pill2.png" alt="Pill Icon" class="pill-icon" />
      <h2>Reset Your <span class="brand">REMEDI</span> Password</h2>
      <p>Enter your email to receive a reset link.</p>
    </div>

    <div class="login-form">
      <form method="post" action="send_pass_reset.php">
        <input type="email" name="email" id="email" placeholder="Email" required />
        <button type="submit">Send Reset Link</button>
      </form>
    </div>
  </div>
</body>
</html>
