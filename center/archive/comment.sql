ALTER TABLE user COMMENT = 'Superclass entity for all dispenser users, subclass is either patient or caregiver, stores basic identifying information';

ALTER TABLE med COMMENT = 'Entity for a medication that a patient is taking, each instance tied to a single user at a time';

ALTER TABLE schedule COMMENT = 'Entity for a medication schedule tied to one user, with many medications. Visualized in webapp.';

ALTER TABLE history COMMENT = 'Entity for tracking consistency of patient taking medications.';

ALTER TABLE notification COMMENT = 'Needed for medication reminders through yet to be determined means';