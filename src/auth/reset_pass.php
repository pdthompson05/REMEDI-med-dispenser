<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rest Password</title>
</head>
<body>
    <div class="reset-container">
        <h1>Reset Password</h1>

        <form method="post" action="send_pass_reset.php">
            <label for="email">Email</label>
            <input type="email" name="email" id="email">

            
            <button type="submit">Send Reset Link</button>
        </form>
    </div>
</body>
</html>