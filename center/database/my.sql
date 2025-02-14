CREATE TABLE `user` (
  `user_id` int PRIMARY KEY AUTO_INCREMENT,
  `first_name` varchar(255),
  `last_name` varchar(255),
  `date_of_birth` date,
  `phone_number` varchar(15),
  `role` enum(patient,caregiver) NOT NULL,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `med` (
  `med_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `name` varchar(255),
  `dosage_mg` int,
  `type` enum(pill,capsule,liquid,injectable,other) NOT NULL,
  `instructions` text,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `schedule` (
  `schedule_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `med_id` int,
  `time` time,
  `frequency` enum(daily,weekly,every_x_days,as_needed) NOT NULL,
  `days` enum(Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday),
  `frequency_x_interval` int,
  `start_date` date,
  `end_date` date,
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `history` (
  `history_id` int PRIMARY KEY AUTO_INCREMENT,
  `schedule_id` int,
  `user_id` int,
  `med_id` int,
  `taken_at` timestamp,
  `missed` boolean
);

CREATE TABLE `notification` (
  `notification_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int,
  `schedule_id` int,
  `reminder_time` datetime,
  `status` enum(pending,acknowledged)
);

ALTER TABLE `med` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `schedule` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `schedule` ADD FOREIGN KEY (`med_id`) REFERENCES `med` (`med_id`) ON DELETE CASCADE;

ALTER TABLE `history` ADD FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`schedule_id`) ON DELETE CASCADE;

ALTER TABLE `history` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `history` ADD FOREIGN KEY (`med_id`) REFERENCES `med` (`med_id`) ON DELETE CASCADE;

ALTER TABLE `notification` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `notification` ADD FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`schedule_id`) ON DELETE CASCADE;