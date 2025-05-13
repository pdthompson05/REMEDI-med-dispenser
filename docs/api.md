## src/config/*

### PHP Config: /src/config/db.php

**Purpose:**  
Initializes a connection to the MySQL database using credentials from the `.env` file.

---

**Behavior:**

- Requires and loads `env.php` to make environment variables available.
- Calls `loadEnv()` on the `.env` file located at the project root.
- Reads the following environment variables:
  - `DB_HOST`
  - `DB_USER`
  - `DB_PASS`
  - `DB_NAME`
  - `MAIL` (optional or unused here)

- Creates a new `mysqli` database connection and stores it in the `$conn` variable.

---

**Failure Behavior:**

- If the connection fails, the script exits with:
  ```
  Database connection failed: {error_message}
  ```

---

**Security Notes:**

- Uses environment variables to avoid hardcoding sensitive data.
- Does not suppress connection errors, which may expose info in production if not handled externally.

### PHP Utility: /src/config/env.php

**Purpose:**  
Defines a `loadEnv()` function that loads environment variables from a `.env` file and makes them available to the PHP runtime.

---

**Function: loadEnv(path)**

**Parameters:**

| Name  | Type   | Required | Description                    |
|-------|--------|----------|--------------------------------|
| path  | string | Yes      | Full path to the `.env` file   |

---

**Behavior:**

- Checks if the file exists at the provided path.
- Reads each line of the file, skipping empty lines.
- Splits each line into key-value pairs using `=`.
- Calls `putenv()` to set each environment variable.

---

**Error Handling:**

- If the file does not exist, the script exits with:
  ```
  Error: .env file not found at {path}
  ```

---

**Security Notes:**

- Does not validate or sanitize values in the `.env` file
- Intended for use in development or controlled environments

## src/auth/user/*

### POST /src/auth/user/login.php

**Purpose:**  
Logs in a user by verifying email and password, then starts a secure session.

---

**Request Parameters (POST):**

| Field      | Type   | Required | Description               |
|------------|--------|----------|---------------------------|
| `email`    | string | Yes      | User’s email              |
| `password` | string | Yes      | User’s plaintext password |

---

**Successful Response:**

```json
{
  "status": "success",
  "message": "Login successful",
  "redirect": "https://section-three.it313communityprojects.website/frontend/html/profile.html"
}
```

---

**Error Responses:**

| Condition                  | Message                                |
|---------------------------|----------------------------------------|
| Missing email or password | "Email and password required"          |
| Invalid credentials       | "Invalid email or password"            |
| Internal error            | Includes `file` and `line` debug info  |

```json
{
  "status": "error",
  "message": "Invalid email or password",
  "debug": {
    "file": "/path/to/file.php",
    "line": 23
  }
}
```

---

**Security Notes:**

- Sessions use `httponly`, `secure`, and `samesite=Lax` cookies
- Stores `REMOTE_ADDR` and `last_activity` in the session


### GET /src/auth/user/logout.php

**Purpose:**  
Logs out the current user by clearing the session and returning a success response.

---

**Request Parameters:**  
_None_

---

**Successful Response:**

```json
{
  "status": "success",
  "message": "Logged out"
}
```

---

**Behavior Notes:**

- Calls `session_unset()` to clear all session variables.
- Calls `session_destroy()` to end the session on the server.
- No redirect is included; frontend is expected to handle post-logout behavior.


### POST /src/auth/user/register.php

**Purpose:**  
Registers a new user by creating entries in both the `user` and `user_profile` tables and sending a verification email.

---

**Request Parameters (POST):**

| Field           | Type   | Required | Description                           |
|----------------|--------|----------|---------------------------------------|
| `first_name`    | string | Yes      | User’s first name                     |
| `last_name`     | string | Yes      | User’s last name                      |
| `email`         | string | Yes      | User’s email                          |
| `password`      | string | Yes      | User’s plaintext password             |
| `date_of_birth` | string | No       | User’s birth date (YYYY-MM-DD)        |

---

**Successful Response:**

```json
{
  "status": "success",
  "message": "Registration successful. Please check your email to verify your account."
}
```

---

**Error Responses:**

| Condition                      | Message                                         |
|-------------------------------|-------------------------------------------------|
| Missing required fields       | "All fields are required."                      |
| Invalid email format          | "Invalid email format."                         |
| Email already registered      | "Email already registered"                      |
| Profile creation failed       | "User profile creation failed"                 |
| Verification email failed     | "Registered successfully, but email could not be sent." |
| General registration error    | "Registration error"                            |

---

**Behavior Notes:**

- Passwords are hashed using `bcrypt`.
- Verification token is hashed with SHA-256.
- Sends verification email with raw token.
- Creates one user entry and one user_profile entry.
- Email verification is handled via `mail.php`.

---

**Security Notes:**

- Uses prepared statements to prevent SQL injection.
- Sanitizes and validates user input.
- Passwords never stored in plain text.


### PHP Utility: /src/auth/user/mail.php

**Purpose:**  
Provides two functions for sending user-related emails using PHPMailer: one for email verification and another for password reset.

---

**Functions:**

#### sendVerificationEmail(email, token)

**Description:**  
Sends a verification email to the given address with a link containing the verification token.

**Parameters:**

| Name   | Type   | Required | Description                    |
|--------|--------|----------|--------------------------------|
| email  | string | Yes      | User’s email address           |
| token  | string | Yes      | Raw email verification token   |

**Behavior:**

- Loads SMTP credentials from `.env`
- Sends an HTML email with a verification link
- Returns `true` if email is sent successfully, `false` otherwise

**Verification link format:**
```
https://section-three.it313communityprojects.website/src/auth/user/token.php?token={token}
```

---

#### sendPasswordResetEmail(email, token)

**Description:**  
Sends a password reset email with a token-based reset link.

**Parameters:**

| Name   | Type   | Required | Description               |
|--------|--------|----------|---------------------------|
| email  | string | Yes      | User’s email address      |
| token  | string | Yes      | Password reset token      |

**Behavior:**

- Loads SMTP credentials from `.env`
- Sends an HTML email with a password reset link
- Returns `true` if email is sent successfully, `false` otherwise

**Reset link format:**
```
https://section-three.it313communityprojects.website/src/auth/forgot_pass.php?token={token}
```

---

**Configuration Notes:**

- SMTP credentials (`MAIL_USER` and `MAIL_PASS`) must be defined in `.env`
- Uses Gmail SMTP (`smtp.gmail.com`, port 587, TLS)
- Errors during send are logged via `error_log` in the catch block


### GET /src/auth/user/token.php

**Purpose:**  
Verifies a user's email by checking the token passed via URL query and updating the user's status in the database.

---

**Request Parameters (GET):**

| Field   | Type   | Required | Description                    |
|---------|--------|----------|--------------------------------|
| `token` | string | Yes      | Email verification token       |

---

**Successful Response (Plain Text):**

```
Email verified successfully.
```

---

**Error Responses (Plain Text):**

- "No token provided."
- "Error verifying email."

---

**Behavior Notes:**

- Hashes the incoming token using SHA-256
- Compares it to `verification_token` in the `user` table where `is_verified = 0`
- If valid:
  - Marks user as verified
  - Clears the `verification_token` field
- If not valid:
  - Responds with a generic error message

---

**Security Notes:**

- No JSON or structured response — this is a plain text endpoint
- Should be used in links from email only

## src/auth/pass/*

### GET /src/auth/pass/forgot_pass.php

**Purpose:**  
Displays a password reset form if a valid password reset token is provided via URL.

---

**Request Parameters (GET):**

| Field   | Type   | Required | Description                           |
|---------|--------|----------|---------------------------------------|
| `token` | string | Yes      | Password reset token from email link  |

---

**Successful Behavior:**

- Validates the token by:
  - Hashing it with SHA-256
  - Checking it against `reset_token_hash` in the `user` table
  - Ensuring `reset_token_expires_at` is in the future
- If valid, displays a password reset HTML form that POSTs to `process_new_pass.php`
- If not valid or expired, displays a plain message: `"Invalid or expired token."`

---

**Error Responses (Plain Text):**

- "No token provided."
- "Invalid or expired token."

---

**Security Notes:**

- Token is passed via GET and not stored in session
- Uses `htmlspecialchars()` to sanitize token in form
- Relies on an expiration timestamp for reset tokens


### GET /src/auth/pass/reset_pass.php

**Purpose:**  
Serves an HTML form where users can request a password reset by submitting their email address.

---

**Form Behavior:**

- Method: `POST`
- Action: `send_pass_reset.php`
- Field:
  - `email` (input type: email, required)

**Displayed To User:**

- Brand title and instructions
- Email input for requesting a password reset link
- Submit button labeled "Send Reset Link"

---

**Usage Notes:**

- This is a static frontend page with no backend logic
- Does not handle any submission or validation itself
- Email is processed in `send_pass_reset.php` after submission


### POST /src/auth/pass/send_pass_reset.php

**Purpose:**  
Generates a password reset token, stores it in the database, and sends a reset link to the user's email.

---

**Request Parameters (POST):**

| Field   | Type   | Required | Description                  |
|---------|--------|----------|------------------------------|
| `email` | string | Yes      | Email address of the user    |

---

**Behavior:**

- Sanitizes the `email` input.
- Generates a 32-character random token.
- Hashes token with SHA-256 and sets a 30-minute expiry.
- Updates the `user` table with:
  - `reset_token_hash`
  - `reset_token_expires_at`
- Calls `sendPasswordResetEmail()` with the raw token.

---

**Successful Response (HTML):**

- If email is sent:
  - "A password reset email has been sent to your email address. Please check your inbox."
  - Link back to login page

- If email fails to send:
  - "Failed to send the password reset email. Please try again."

---

**Error Response:**

- If email not found:
  - "No user found with that email address."

---

**Security Notes:**

- Uses `random_bytes()` for cryptographic randomness
- Stores only hashed tokens in the database
- Returns plaintext messages, not JSON


### PHP Utility: /src/auth/pass/mail.php

**Purpose:**  
Provides two functions for sending user-related emails using PHPMailer: one for email verification and another for password reset.

---

**Functions:**

#### sendVerificationEmail(email, token)

**Description:**  
Sends a verification email to the given address with a link containing the verification token.

**Parameters:**

| Name   | Type   | Required | Description                    |
|--------|--------|----------|--------------------------------|
| email  | string | Yes      | User’s email address           |
| token  | string | Yes      | Raw email verification token   |

**Behavior:**

- Loads SMTP credentials from `.env`
- Sends an HTML email with a verification link
- Returns `true` if email is sent successfully, `false` otherwise

**Verification link format:**
```
https://section-three.it313communityprojects.website/src/auth/verify_token.php?token={token}
```

---

#### sendPasswordResetEmail(email, token)

**Description:**  
Sends a password reset email with a token-based reset link.

**Parameters:**

| Name   | Type   | Required | Description               |
|--------|--------|----------|---------------------------|
| email  | string | Yes      | User’s email address      |
| token  | string | Yes      | Password reset token      |

**Behavior:**

- Loads SMTP credentials from `.env`
- Sends an HTML email with a password reset link
- Returns `true` if email is sent successfully, `false` otherwise

**Reset link format:**
```
https://section-three.it313communityprojects.website/src/auth/forgot_pass.php?token={token}
```

---

**Configuration Notes:**

- SMTP credentials (`MAIL_USER` and `MAIL_PASS`) must be defined in `.env`
- Uses Gmail SMTP (`smtp.gmail.com`, port 587, TLS)
- Errors during send are logged via `error_log` in the catch block


### POST /src/auth/pass/process_new_pass.php

**Purpose:**  
Processes a password reset using a valid token and updates the user's password in the database.

---

**Request Parameters (POST):**

| Field           | Type   | Required | Description                              |
|----------------|--------|----------|------------------------------------------|
| `new_password`  | string | Yes      | The new password to set (min 8 chars)    |
| `token`         | string | Yes      | The password reset token from email link |

---

**Behavior:**

- Validates presence of `token` and `new_password`.
- Hashes the token using SHA-256.
- Checks token validity and expiration in `user` table.
- Validates password length (≥ 8 characters).
- Hashes the new password using `password_hash`.
- Updates `password_hash`, clears `reset_token_hash` and `reset_token_expires_at`.

---

**Successful Response (Plain Text):**

```
Password updated successfully. You can now login.
```

---

**Error Responses (Plain Text):**

- "Missing required fields."
- "No token provided."
- "Token invalid or expired."
- "Password must be at least 8 characters long."
- "Error updating password."
- "Invalid request method."

---

**Security Notes:**

- Tokens are hashed before being stored and compared
- Reset tokens are one-time use and expire after 30 minutes
- Uses `password_hash()` for secure password storage
