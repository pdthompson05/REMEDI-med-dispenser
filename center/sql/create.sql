DROP DATABASE IF EXISTS db_remedi;
CREATE DATABASE db_remedi;
USE db_remedi;

DROP TABLE IF EXISTS user_profile;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS med;
DROP TABLE IF EXISTS schedule;
DROP TABLE IF EXISTS history;
DROP TABLE IF EXISTS notification;

CREATE TABLE user (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE user_profile (
  profile_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT UNIQUE NOT NULL,
  first_name VARCHAR(255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  caretaker_name VARCHAR(255) NULL,
  caretaker_email VARCHAR(255) NULL,
  date_of_birth DATE NULL,
  profile_picture VARCHAR(255) NULL,
  user_role ENUM('patient', 'caregiver') NULL,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

CREATE TABLE med (
  med_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  dosage_mg INT,
  type ENUM('pill', 'capsule', 'liquid', 'injectable', 'other') NOT NULL,
  instructions TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

CREATE TABLE schedule (
  schedule_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  med_id INT NOT NULL,
  scheduled_time DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
  FOREIGN KEY (med_id) REFERENCES med(med_id) ON DELETE CASCADE
);

CREATE TABLE history (
  history_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  med_id INT NOT NULL,
  taken_at DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
  FOREIGN KEY (med_id) REFERENCES med(med_id) ON DELETE CASCADE
);

CREATE TABLE notification (
  notification_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  message TEXT NOT NULL,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);
