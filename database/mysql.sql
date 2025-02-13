CREATE TABLE `user` (
  `user_id` int PRIMARY KEY,
  `first_name` varchar(255),
  `last_name` varchar(255),
  `date_of_birth` date,
  `phone_number` varchar(15),
  `email` varchar(255),
  `user_type` varchar(50),
  `created` timestamp,
  `updated` timestamp
);

CREATE TABLE `medication` (
  `medication_id` int PRIMARY KEY,
  `name` varchar(255),
  `dosage` varchar(50),
  `prescription_required` boolean,
  `created` timestamp,
  `updated` timestamp
);

CREATE TABLE `device` (
  `device_id` int PRIMARY KEY,
  `user_id` int,
  `created` timestamp,
  `updated` timestamp
);

CREATE TABLE `schedule` (
  `schedule_id` int PRIMARY KEY,
  `user_id` int,
  `medication_id` int,
  `dispenser_id` int,
  `frequency` varchar(50),
  `dosage_amount` varchar(50),
  `missed_doses` int,
  `status` varchar(50)
);

ALTER TABLE `device` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

ALTER TABLE `schedule` ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

ALTER TABLE `schedule` ADD FOREIGN KEY (`medication_id`) REFERENCES `medication` (`medication_id`);

ALTER TABLE `schedule` ADD FOREIGN KEY (`dispenser_id`) REFERENCES `device` (`device_id`);
