CREATE TABLE user (
  id int PRIMARY KEY AUTO_INCREMENT,
  user_role enum(patient,caregiver) NOT NULL,
  username varchar(255) UNIQUE NOT NULL,
  password_hash varchar(255) NOT NULL,
  first_name varchar(255),
  last_name varchar(255),
  email varchar(255),
  date_of_birth date,
  created_at timestamp,
  updated_at timestamp
);

CREATE TABLE med (
  id int PRIMARY KEY AUTO_INCREMENT,
  user_id int,
  name varchar(255),
  dosage_mg int,
  type enum(pill,capsule,liquid,injectable,other) NOT NULL,
  instructions text,
  created_at timestamp,
  updated_at timestamp
);

CREATE TABLE schedule (
  id int PRIMARY KEY AUTO_INCREMENT,
  user_id int,
  med_id int,
  time time,
  frequency enum(daily,weekly,every_x_days,as_needed) NOT NULL,
  days enum(Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday),
  frequency_x_interval int,
  start_date date,
  end_date date,
  created_at timestamp,
  updated_at timestamp
);

CREATE TABLE history (
  id int PRIMARY KEY AUTO_INCREMENT,
  schedule_id int,
  user_id int,
  med_id int,
  taken_at timestamp,
  missed boolean
);

CREATE TABLE notification (
  id int PRIMARY KEY AUTO_INCREMENT,
  user_id int,
  schedule_id int,
  reminder_time datetime,
  status enum(pending,acknowledged)
);