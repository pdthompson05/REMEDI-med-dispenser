Table user {
  user_id int [pk]
  first_name varchar(255)
  last_name varchar(255)
  date_of_birth date
  phone_number varchar(15)
  email varchar(255)
  user_type varchar(50)
  created timestamp
  updated timestamp
}

Table medication {
  medication_id int [pk]
  name varchar(255)
  dosage varchar(50)
  prescription_required boolean
  created timestamp
  updated timestamp
}

Table device {
  device_id int [pk]
  user_id int [ref: > user.user_id]
  created timestamp
  updated timestamp
}

Table schedule {
  schedule_id int [pk]
  user_id int [ref: > user.user_id]
  medication_id int [ref: > medication.medication_id]
  dispenser_id int [ref: > device.device_id]
  frequency varchar(50)
  dosage_amount varchar(50)
  missed_doses int
  status varchar(50)
}