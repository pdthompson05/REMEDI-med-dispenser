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

## src/routes/*

### GET /src/routes/profile/load.php

**Purpose:**  
Loads the profile data for the currently authenticated user, including account and profile details.

---

**Request Parameters:**  
_None_

---

**Success Response:**

```json
{
  "status": "success",
  "data": {
    "email": "user@example.com",
    "first_name": "John",
    "last_name": "Doe",
    "date_of_birth": "1990-01-01",
    "caretaker_name": "Jane Doe",
    "caretaker_email": "jane@example.com",
    "profile_picture": "https://section-three.it313communityprojects.website/center/php/profile.jpg"
  }
}
```

If no profile picture is set, a placeholder URL is returned instead.

---

**Error Responses:**

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "User not logged in"
  }
  ```

- Profile not found:
  ```json
  {
    "status": "error",
    "message": "Profile not found"
  }
  ```

---

**Behavior Notes:**

- Queries the `user` and `user_profile` tables
- Joins on `user_id` to gather account and profile information
- Replaces missing profile picture with a default placeholder URL

---

**Security Notes:**

- Requires session-based authentication (`$_SESSION['user_id']`)
- Uses prepared statements to protect against SQL injection

### POST /src/routes/profile/update.php

**Purpose:**  
Updates the authenticated user's profile and account information, including optional profile picture upload.

---

**Request Parameters (POST):**

| Name              | Type   | Required | Description                                     |
|-------------------|--------|----------|-------------------------------------------------|
| `first_name`      | string | Yes      | User's first name                               |
| `last_name`       | string | Yes      | User's last name                                |
| `email`           | string | Yes      | User's updated email address                    |
| `date_of_birth`   | string | No       | Birthdate (YYYY-MM-DD)                          |
| `caretaker_name`  | string | No       | Name of caretaker                               |
| `caretaker_email` | string | No       | Email of caretaker                              |
| `profile_picture` | file   | No       | JPG/PNG/GIF image to set as profile picture     |

---

**Success Response:**

```json
{
  "status": "success",
  "message": "Profile updated successfully"
}
```

---

**Error Responses:**

- Missing required fields:
  ```json
  {
    "status": "error",
    "message": "Required fields are missing"
  }
  ```

- Duplicate email:
  ```json
  {
    "status": "error",
    "message": "Email is already in use"
  }
  ```

- Invalid image file:
  ```json
  {
    "status": "error",
    "message": "Invalid file type. Only JPG, PNG, and GIF are allowed."
  }
  ```

- File upload failed:
  ```json
  {
    "status": "error",
    "message": "Failed to upload profile picture."
  }
  ```

- SQL error:
  ```json
  {
    "status": "error",
    "message": "SQL Error: {details}"
  }
  ```

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "User not logged in"
  }
  ```

---

**Behavior Notes:**

- Updates both `user` (email) and `user_profile` tables
- Stores uploaded image in `uploads/profile_pictures/` and stores relative path
- Retains old image if no new file is uploaded

---

**Security Notes:**

- Requires user authentication via `$_SESSION['user_id']`
- Uses prepared statements for all DB operations
- Validates file extensions to prevent malicious uploads

### GET /src/routes/med/get.php

**Purpose:**  
Retrieves all medications associated with the currently logged-in user.

---

**Request Parameters:**  
_None_

---

**Success Response:**

```json
{
  "status": "success",
  "data": [
    {
      "med_id": 1,
      "med_name": "Ibuprofen",
      "strength": "200mg",
      "rx_number": "RX12345",
      "quantity": 30
    },
    ...
  ]
}
```

---

**Error Responses:**

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "User not logged in"
  }
  ```

---

**Behavior Notes:**

- Data is fetched from the `med` table using `user_id` from session
- Results are returned as an array of medication objects

---

**Security Notes:**

- Requires active user session (`$_SESSION['user_id']`)
- Uses prepared statements to prevent SQL injection

### POST /src/routes/med/add.php

**Purpose:**  
Adds a new medication record for the logged-in user.

---

**Request Parameters (POST):**

| Name        | Type   | Required | Description                         |
|-------------|--------|----------|-------------------------------------|
| `med_name`  | string | Yes      | Name of the medication              |
| `strength`  | string | Yes      | Dosage strength (e.g., "500mg")     |
| `rx_number` | string | Yes      | Prescription number                 |
| `quantity`  | int    | Yes      | Number of pills/tablets             |

---

**Success Response:**

```json
{
  "status": "success",
  "message": "Medication added successfully"
}
```

---

**Error Responses:**

- Missing fields or invalid quantity:
  ```json
  {
    "status": "error",
    "message": "All fields are required and quantity must be greater than 0"
  }
  ```

- SQL failure:
  ```json
  {
    "status": "error",
    "message": "SQL Error: {details}"
  }
  ```

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "User not logged in"
  }
  ```

---

**Security Notes:**

- Requires session-based authentication (`$_SESSION['user_id']`)
- Uses prepared statements to prevent SQL injection
- All fields are trimmed and validated for non-empty content

### POST /src/routes/med/delete.php

**Purpose:**  
Deletes a medication record for the logged-in user.

---

**Request Parameters (POST):**

| Name     | Type | Required | Description            |
|----------|------|----------|------------------------|
| `med_id` | int  | Yes      | ID of the medication   |

---

**Success Response:**

```json
{
  "status": "success",
  "message": "Medication deleted"
}
```

---

**Error Responses:**

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "Not logged in"
  }
  ```

- Invalid med_id:
  ```json
  {
    "status": "error",
    "message": "Invalid med ID",
    "debug": {
      "raw_post": { "med_id": "..." },
      "received_med_id": "..."
    }
  }
  ```

- SQL failure:
  ```json
  {
    "status": "error",
    "message": "Failed to delete"
  }
  ```

---

**Security Notes:**

- Requires active session (`$_SESSION['user_id']`)
- Medication is only deleted if it belongs to the authenticated user
- Uses prepared statements to avoid SQL injection

### GET /src/routes/reminder/get.php

**Purpose:**  
Retrieves all reminders for the authenticated user, including medication name and associated reminder times.

---

**Request Parameters:**  
_None_

---

**Success Response:**

```json
{
  "status": "success",
  "data": [
    {
      "reminder_id": 1,
      "med_id": 2,
      "med_name": "Ibuprofen",
      "dosage": "1 pill",
      "reminder_type": "interval",
      "interval_hours": 8,
      "start_date": "2024-05-01",
      "end_date": "2024-05-10",
      "times": ["08:00:00", "16:00:00", "00:00:00"]
    },
    ...
  ]
}
```

---

**Error Responses:**

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "Not logged in"
  }
  ```

---

**Behavior Notes:**

- Joins `reminder` with `med` and `reminder_times`
- Groups `reminder_time` entries into a `times` array for each `reminder_id`
- Sorts reminders by `start_date` and each time by `reminder_time`

---

**Security Notes:**

- Requires session-based authentication (`$_SESSION['user_id']`)
- Uses prepared statements to prevent SQL injection


### POST /src/routes/reminder/add.php

**Purpose:**  
Creates a medication reminder and automatically schedules related calendar events based on the reminder type.

---

**Request Parameters (POST):**

| Name             | Type     | Required | Description                                              |
|------------------|----------|----------|----------------------------------------------------------|
| `med_id`         | int      | Yes      | ID of the medication                                     |
| `dosage`         | string   | No       | Dosage instructions (e.g., "1 pill")                     |
| `reminder_type`  | string   | Yes      | Type of reminder (`specific` or `interval`)              |
| `interval_hours` | int      | Conditionally | Required if `reminder_type` is `interval`           |
| `start_date`     | string   | Yes      | Start date in `YYYY-MM-DD` format                        |
| `end_date`       | string   | Yes      | End date in `YYYY-MM-DD` format                          |
| `times`          | string[] | Conditionally | Required if `reminder_type` is `specific`          |

---

**Success Response:**

```json
{
  "status": "success",
  "message": "Reminder and events created"
}
```

---

**Error Responses:**

- Missing required fields:
  ```json
  {
    "status": "error",
    "message": "Missing required fields"
  }
  ```

- Invalid medication:
  ```json
  {
    "status": "error",
    "message": "Invalid med ID"
  }
  ```

- Invalid reminder type or configuration:
  ```json
  {
    "status": "error",
    "message": "Invalid reminder type or data"
  }
  ```

- General failure:
  ```json
  {
    "status": "error",
    "message": "Failed to save reminder"
  }
  ```

---

**Behavior Notes:**

- Validates medication ownership before scheduling
- Inserts a new `reminder` record
- If `specific`, stores times in `reminder_times` and generates daily events for each time
- If `interval`, calculates time-based events between start and end date
- Adds all reminder events to the `calendar_events` table with medication title

---

**Security Notes:**

- Requires session-based authentication (`$_SESSION['user_id']`)
- Uses prepared statements throughout to prevent SQL injection


### SCRIPT: /src/routes/reminder/send.php

**Purpose:**  
Sends email reminders to users for medication events scheduled within the next 10 minutes. Intended to be run as a scheduled background job (e.g., via cron).

---

**How It Works:**

1. Loads email credentials from `.env`.
2. Finds all events in `calendar_events` where `event_datetime` is within the next 10 minutes.
3. Joins each event with the corresponding `user` and `med` to get the user's email and med name.
4. Sends an email to each user via PHPMailer.

---

**Email Content:**

- **Subject:** `Medication Reminder`
- **Body:**  
  ```
  It's time to take your medication: {med_name}
  Scheduled time: {event_datetime}
  ```

---

**PHPMailer Configuration:**

- SMTP Host: `smtp.gmail.com`
- Port: `587`
- Auth: Yes (TLS)
- Credentials: Loaded from `.env` (`MAIL_USER`, `MAIL_PASS`)

---

**Security Notes:**

- Email credentials are not hardcoded; they are loaded from the environment.
- Uses `PHPMailer` with TLS and authenticated SMTP.
- Errors during mail send are logged via `error_log`.

---

**Development Notes:**

- This script is a partial implementation.
- Intended to later connect to a user notification tray in the frontend.
- Currently does not log successful sends or retry failed ones.

---

**Recommended Use:**

- Run every 5–10 minutes via a cron job or background scheduler.
- Ensure your `.env` file is correctly configured and email access is authorized (e.g., App Passwords if using Gmail).


### GET /src/routes/reminder/upcoming.php

**Purpose:**  
Fetches medication events scheduled within the next 10 minutes for the logged-in user. This endpoint is intended to support a frontend notification tray feature.

---

**Request Parameters:**  
_None_

---

**Success Response:**

```json
{
  "status": "success",
  "data": [
    {
      "med_name": "Ibuprofen",
      "event_datetime": "2024-05-12 08:00:00"
    },
    ...
  ]
}
```

---

**Error Responses:**

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "Not logged in"
  }
  ```

---

**Behavior Notes:**

- Requires user session (`$_SESSION['user_id']`)
- Queries `calendar_events` for upcoming events in the next 10 minutes
- Joins with the `med` table to retrieve the medication name
- Sorts events in ascending order by time

---

**Development Notes:**

- Meant to support frontend user notifications (e.g., badge alerts)
- Does not include reminder ID or title—just med name and time
- Can be extended to include device ID, icon, or user preference filtering

---

**Security Notes:**

- Uses prepared statements to prevent SQL injection
- Only returns events belonging to the authenticated user


### Endpoint: /src/routes/calendar/render.php

**Purpose:**  
Handles calendar event operations (view, create, update, delete) for authenticated users.

---

## Supported Methods

### GET

**Description:**  
Fetches all events for the logged-in user. Supports optional date filtering.

**Query Parameters:**

| Name         | Required | Description                             |
|--------------|----------|-----------------------------------------|
| `start_date` | No       | Filter start (YYYY-MM-DD format)        |
| `end_date`   | No       | Filter end (YYYY-MM-DD format)          |

**Success Response:**
```json
{
  "status": "success",
  "data": [ { "event_id": 1, "med_id": 2, "event_datetime": "...", "med_name": "..." }, ... ]
}
```

---

### POST

**Description:**  
Adds a new event to the calendar.

**POST Parameters:**

| Name            | Required | Description                      |
|-----------------|----------|----------------------------------|
| `med_id`        | Yes      | ID of the medication             |
| `event_datetime`| Yes      | Date and time of the event (UTC) |

**Success Response:**
```json
{
  "status": "success",
  "message": "Event added",
  "event_id": 12
}
```

---

### PUT

**Description:**  
Updates an existing event's medication or datetime.

**PUT Parameters:**

| Name            | Required | Description                     |
|-----------------|----------|---------------------------------|
| `event_id`      | Yes      | ID of the event to update       |
| `med_id`        | Yes      | Updated medication ID           |
| `event_datetime`| Yes      | New datetime for the event      |

**Success Response:**
```json
{
  "status": "success",
  "message": "Event updated"
}
```

---

### DELETE

**Description:**  
Deletes an event and its associated reminder(s).

**DELETE Parameters:**

| Name       | Required | Description               |
|------------|----------|---------------------------|
| `event_id` | Yes      | ID of the event to delete |

**Success Response:**
```json
{
  "status": "success",
  "message": "Event and reminder deleted"
}
```

---

## General Error Responses

- Not logged in
- Missing fields
- Event not found
- Method not allowed

**All responses are JSON-formatted.**

---

**Security Notes:**

- Requires active session with `$_SESSION['user_id']`
- Event operations are scoped to the authenticated user

## src/routes/device/*

### POST /src/routes/device/pair.php

**Purpose:**  
Pairs a device to the currently logged-in user if it is not already paired.

---

**Request Parameters (POST):**

| Name        | Type | Required | Description              |
|-------------|------|----------|--------------------------|
| `device_id` | int  | Yes      | ID of the device to pair |

---

**Success Response:**

```json
{
  "status": "success"
}
```

---

**Error Responses:**

- Missing device ID:
  ```json
  {
    "status": "error",
    "message": "Missing device ID"
  }
  ```

- Device already paired or pairing failed:
  ```json
  {
    "status": "error",
    "message": "Pairing failed. Device may already be paired."
  }
  ```

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "Not logged in"
  }
  ```

---

**Behavior Notes:**

- Only allows pairing if `user_id` on the device is `NULL`
- Sets `$_SESSION['device_id']` upon successful pairing
- Uses prepared statements for secure update

---

**Security Notes:**

- Requires session-based authentication (`$_SESSION['user_id']`)
- Device pairing is only allowed once to prevent hijacking


### POST /src/routes/device/unpair.php

**Purpose:**  
Unpairs a device from the currently authenticated user by setting the device's `user_id` to NULL.

---

**Request Parameters (POST):**

| Name        | Type | Required | Description                                         |
|-------------|------|----------|-----------------------------------------------------|
| `device_id` | int  | No       | Device ID (optional if stored in session variable)  |

---

**Success Response:**

```json
{
  "status": "success"
}
```

---

**Error Responses:**

- No valid device ID:
  ```json
  {
    "status": "error",
    "message": "No valid device ID"
  }
  ```

- Unpairing failed:
  ```json
  {
    "status": "error",
    "message": "Unpairing failed"
  }
  ```

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "Not logged in"
  }
  ```

---

**Behavior Notes:**

- Device ID can be provided via session or POST
- Successfully unpaired devices have `user_id` set to `NULL`
- Upon success, removes `device_id` from session

---

**Security Notes:**

- Requires valid session with `$_SESSION['user_id']`
- Unpairs only devices that belong to the user (`user_id` check enforced)
- Uses prepared statements to avoid SQL injection


### GET /src/routes/device/fetch_status.php

**Purpose:**  
Retrieves the current device status for the authenticated user, including connection status and temperature.

---

**Request Parameters:**  
_None_

---

**Success Response (Device Found):**

```json
{
  "status": "success",
  "device": {
    "device_id": 1,
    "connected": 1,
    "temperature": 23.5
  }
}
```

**Success Response (No Device Found):**

```json
{
  "status": "success",
  "device": null
}
```

---

**Error Responses:**

- Not authenticated:
  ```json
  {
    "status": "error",
    "message": "Not authenticated"
  }
  ```

- SQL failure:
  ```json
  {
    "status": "error",
    "message": "SQL error message here"
  }
  ```

---

**Behavior Notes:**

- Looks up the device assigned to the current user (`user_id`)
- Returns null if no device is found
- Uses session-based authentication

---

**Security Notes:**

- Requires `$_SESSION['user_id']` to be set
- Uses prepared statements to avoid SQL injection


### GET /src/routes/device/get_config.php

**Purpose:**  
Fetches device configuration and sensor slot details for the currently authenticated user.

---

**Request Parameters:**  
_None_

---

**Success Response:**

```json
{
  "status": "success",
  "data": [
    {
      "device_id": 1,
      "pairing_code": "abc123",
      "connected": 1,
      "slots": [
        {
          "sensor_id": 1,
          "med_name": "Ibuprofen",
          "med_count": 30
        },
        ...
      ]
    }
  ]
}
```

---

**Error Responses:**

- Not authorized:
  ```json
  {
    "status": "error",
    "message": "Not authorized"
  }
  ```

---

**Behavior Notes:**

- Joins the `device`, `sensor`, and `med` tables
- Groups sensor slot information under each device entry
- Returns all devices tied to the currently authenticated user

---

**Security Notes:**

- Requires `$_SESSION['user_id']` to be set
- Uses prepared statements to prevent SQL injection


### GET /src/routes/device/config_sensor.php

**Purpose:**  
Returns the current sensor slot configuration for a user's paired device, along with all medications available to the user.

---

**Request Parameters:**  
_None_

---

**Success Response:**

```json
{
  "status": "success",
  "device_id": 1,
  "slots": {
    "1": { "med_id": 2, "med_name": "Ibuprofen", "med_count": 30 },
    "2": { "med_id": 3, "med_name": "Paracetamol", "med_count": 20 },
    ...
  },
  "meds": [
    { "med_id": 2, "med_name": "Ibuprofen" },
    { "med_id": 3, "med_name": "Paracetamol" }
  ]
}
```

---

**Error Responses:**

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "Not logged in"
  }
  ```

- No paired device:
  ```json
  {
    "status": "error",
    "message": "No paired device"
  }
  ```

---

**Behavior Notes:**

- Checks for a device paired to the user
- Retrieves sensor slot configurations (by slot number) and associated medication info
- Queries and returns a list of all medications owned by the user

---

**Security Notes:**

- Requires active session with `$_SESSION['user_id']`
- All SQL operations use prepared statements


### POST /src/routes/device/submit_sensor.php

**Purpose:**  
Configures up to 4 sensor slots on a paired device, each linked to a specific medication and count.

---

**Request Parameters (POST):**

| Name                 | Type | Required | Description                                      |
|----------------------|------|----------|--------------------------------------------------|
| `device_id`          | int  | No       | Optional if stored in session                   |
| `slot_1_med_id`      | int  | Conditionally | Medication ID for slot 1                   |
| `slot_1_count`       | int  | Conditionally | Initial count for slot 1                    |
| `slot_2_med_id`      | int  | Conditionally | Medication ID for slot 2                   |
| `slot_2_count`       | int  | Conditionally | Initial count for slot 2                    |
| `slot_3_med_id`      | int  | Conditionally | Medication ID for slot 3                   |
| `slot_3_count`       | int  | Conditionally | Initial count for slot 3                    |
| `slot_4_med_id`      | int  | Conditionally | Medication ID for slot 4                   |
| `slot_4_count`       | int  | Conditionally | Initial count for slot 4                    |

---

**Success Response:**

```json
{
  "status": "success",
  "message": "Sensor slots configured"
}
```

---

**Error Responses:**

- Not logged in:
  ```json
  {
    "status": "error",
    "message": "Not logged in"
  }
  ```

- No paired device:
  ```json
  {
    "status": "error",
    "message": "No paired device"
  }
  ```

- Invalid input or unauthorized medication:
  ```json
  {
    "status": "error",
    "message": "Invalid input"
  }
  ```

---

**Behavior Notes:**

- Validates that each `med_id` belongs to the current user
- Uses `INSERT ... ON DUPLICATE KEY UPDATE` to update existing slots
- Supports partial submission (1–4 slots)
- If no valid slot data is provided, returns error

---

**Security Notes:**

- Requires active session (`$_SESSION['user_id']`)
- Prevents setting meds not owned by user
- Uses prepared statements to prevent SQL injection