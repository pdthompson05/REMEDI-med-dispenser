USE db_remidi;

DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS med;
DROP TABLE IF EXISTS schedule;
DROP TABLE IF EXISTS history;
DROP TABLE IF EXISTS notification;

--

CREATE TABLE user (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  user_role ENUM('patient', 'caregiver') NOT NULL,
  username VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  first_name VARCHAR(255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  date_of_birth DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
  med_id INT,
  time TIME,
  frequency ENUM('daily', 'weekly', 'every_x_days', 'as_needed') NOT NULL,
  days SET('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
  start_date DATE,
  end_date DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
  FOREIGN KEY (med_id) REFERENCES med(med_id) ON DELETE CASCADE
);

CREATE TABLE history (
  history_id INT PRIMARY KEY AUTO_INCREMENT,
  schedule_id INT NOT NULL,
  user_id INT NOT NULL,
  med_id INT NOT NULL,
  taken_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  missed BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (schedule_id) REFERENCES schedule(schedule_id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
  FOREIGN KEY (med_id) REFERENCES med(med_id) ON DELETE CASCADE
);

CREATE TABLE notification (
  notification_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  schedule_id INT NOT NULL,
  reminder_time DATETIME NOT NULL,
  status ENUM('pending', 'acknowledged') DEFAULT 'pending',
  FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
  FOREIGN KEY (schedule_id) REFERENCES schedule(schedule_id) ON DELETE CASCADE
);

ALTER TABLE user COMMENT = 'Superclass entity for all dispenser users, subclass is either patient or caregiver, stores basic identifying information';

ALTER TABLE med COMMENT = 'Entity for a medication that a patient is taking, each instance tied to a single user at a time';

ALTER TABLE schedule COMMENT = 'Entity for a medication schedule tied to one user, with many medications. Visualized in webapp.';

ALTER TABLE history COMMENT = 'Entity for tracking consistency of patient taking medications.';

ALTER TABLE notification COMMENT = 'Needed for medication reminders through yet to be determined means';