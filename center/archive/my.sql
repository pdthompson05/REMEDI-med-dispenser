CREATE TABLE user (
  user_id int PRIMARY KEY AUTO_INCREMENT,
  first_name varchar(255),
  last_name varchar(255),
  date_of_birth date,
  phone_number varchar(15),
  role enum(patient,caregiver) NOT NULL,
  created_at timestamp,
  updated_at timestamp
);

CREATE TABLE med (
  med_id int PRIMARY KEY AUTO_INCREMENT,
  user_id int,
  name varchar(255),
  dosage_mg int,
  type enum(pill,capsule,liquid,injectable,other) NOT NULL,
  instructions text,
  created_at timestamp,
  updated_at timestamp
);

CREATE TABLE schedule (
  schedule_id int PRIMARY KEY AUTO_INCREMENT,
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
  history_id int PRIMARY KEY AUTO_INCREMENT,
  schedule_id int,
  user_id int,
  med_id int,
  taken_at timestamp,
  missed boolean
);

CREATE TABLE notification (
  notification_id int PRIMARY KEY AUTO_INCREMENT,
  user_id int,
  schedule_id int,
  reminder_time datetime,
  status enum(pending,acknowledged)
);

--

ALTER TABLE user COMMENT = 'Superclass entity for all dispenser users, subclass is either patient or caregiver, stores basic identifying information';

ALTER TABLE med COMMENT = 'Entity for a medication that a patient is taking, each instance tied to a single user at a time';

ALTER TABLE schedule COMMENT = 'Entity for a medication schedule tied to one user, with many medications. Visualized in webapp.';

ALTER TABLE history COMMENT = 'Entity for tracking consistency of patient taking medications.';

ALTER TABLE notification COMMENT = 'Needed for medication reminders through yet to be determined means';

--

ALTER TABLE med ADD FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE;

ALTER TABLE schedule ADD FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE;

ALTER TABLE schedule ADD FOREIGN KEY (med_id) REFERENCES med (med_id) ON DELETE CASCADE;

ALTER TABLE history ADD FOREIGN KEY (schedule_id) REFERENCES schedule (schedule_id) ON DELETE CASCADE;

ALTER TABLE history ADD FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE;

ALTER TABLE history ADD FOREIGN KEY (med_id) REFERENCES med (med_id) ON DELETE CASCADE;

ALTER TABLE notification ADD FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE;

ALTER TABLE notification ADD FOREIGN KEY (schedule_id) REFERENCES schedule (schedule_id) ON DELETE CASCADE;