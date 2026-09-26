ALTER TABLE lhpschool
    ADD COLUMN about_school TEXT NULL,
    ADD COLUMN school_sections VARCHAR(255) NOT NULL DEFAULT '',
    ADD COLUMN school_photo VARCHAR(88) NOT NULL DEFAULT '',
    ADD COLUMN admissions_message TEXT NULL;
