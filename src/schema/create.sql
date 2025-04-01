DROP DATABASE IF EXISTS db_remedi;
CREATE DATABASE db_remedi;
USE db_remedi;

DROP TABLE IF EXISTS user_profile;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS med;
DROP TABLE IF EXISTS schedule;
DROP TABLE IF EXISTS history;
DROP TABLE IF EXISTS notification;

-- Fixed user table with verification columns
CREATE TABLE user (
  user_id INT PRIMARY KEY AUTO_INCREMENT,  -- Fixed typo
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  verification_token VARCHAR(64),          -- Added for email verification
  is_verified TINYINT(1) DEFAULT 0,        -- Added (0=false, 1=true)
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Fixed table name (was missing)
CREATE TABLE user_profile (
  profile_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT UNIQUE NOT NULL,
  first_name VARCHAR(255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  caretaker_name VARCHAR(255),
  caretaker_email VARCHAR(255),
  date_of_birth DATE,
  profile_picture VARCHAR(255),
  user_role ENUM('patient', 'caregiver'),
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

-- Rest of tables remain the same
CREATE TABLE med (
  med_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  med_name VARCHAR(255) NOT NULL,
  strength VARCHAR(100) NOT NULL,           -- From your 'med-strength' input
  rx_number VARCHAR(100) NOT NULL,          -- From your 'rx-number' input
  quantity INT NOT NULL,                    -- From your 'quantity' input
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

CREATE TABLE reminder (
  reminder_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  med_id INT NOT NULL,
  dosage VARCHAR(100) DEFAULT NULL,
  reminder_type ENUM('specific', 'interval') NOT NULL,
  interval_hours INT DEFAULT NULL,                  -- Only used if type = interval
  reminder_time TIME DEFAULT NULL,                  -- Used if type = specific
  reminder_date DATE NOT NULL,                      -- For each day between start/end
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
  FOREIGN KEY (med_id) REFERENCES med(med_id) ON DELETE CASCADE
);

