Table user {
  user_id int [pk, increment]
  first_name varchar(255)
  last_name varchar(255)
  date_of_birth date
  phone_number varchar(15)
  role enum('patient', 'caregiver') [not null]
  created_at timestamp
  updated_at timestamp
}

Table med {
  med_id int [pk, increment]
  user_id int
  name varchar(255)
  dosage_mg int
  type enum('pill', 'capsule', 'liquid', 'injectable', 'other') [not null]
  instructions text
  created_at timestamp
  updated_at timestamp
}

Table schedule {
  schedule_id int [pk, increment]
  user_id int
  med_id int
  time time
  frequency enum('daily', 'weekly', 'every_x_days', 'as_needed') [not null]
  days enum('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') [null]
  frequency_x_interval int [null]
  start_date date
  end_date date
  created_at timestamp
  updated_at timestamp
}

Table history {
  history_id int [pk, increment]
  schedule_id int
  user_id int
  med_id int
  taken_at timestamp
  missed boolean
}

Table notification {
  notification_id int [pk, increment]
  user_id int
  schedule_id int
  reminder_time datetime
  status enum('pending', 'acknowledged')
}

Ref: med.user_id > user.user_id [delete: cascade]
Ref: schedule.user_id > user.user_id [delete: cascade]
Ref: schedule.med_id > med.med_id [delete: cascade]
Ref: history.schedule_id > schedule.schedule_id [delete: cascade]
Ref: history.user_id > user.user_id [delete: cascade]
Ref: history.med_id > med.med_id [delete: cascade]
Ref: notification.user_id > user.user_id [delete: cascade]
Ref: notification.schedule_id > schedule.schedule_id [delete: cascade]