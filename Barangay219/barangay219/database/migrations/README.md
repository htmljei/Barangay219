# Database Migrations

Run **before** using the new thesis-adviser features (resident approval, extended fields, blotter scheduling, secretary approval permission).

## 001_thesis_requirements.sql

Adds:

- **residents**: place_of_birth, length_of_stay_years, date_of_residency, email, monthly_income, employment_type, income_source, is_pwd, is_senior, SSS/PhilHealth/GSIS/TIN/voter_id/precinct_number, blood_type, allergies, medical_conditions, disability, relationship_to_head, approval_status, registration_type, submitted_by_user_id, approved_by_user_id, approved_at
- **users**: can_approve_registration (TINYINT, for secretary; captain assigns this)
- **blotters**: hearing_date, hearing_notes

**How to run (e.g. phpMyAdmin):**

1. Open the SQL tab for database `barangay219_db`.
2. Paste and run the contents of `001_thesis_requirements.sql`.
3. If you see “Duplicate column name”, that part is already applied; you can run the remaining statements one by one.

Existing rows keep their data; new columns get defaults.
