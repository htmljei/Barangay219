-- E-Barangay Information Management System
-- Migration: Thesis Adviser Requirements
-- Run this once on your existing database. New columns only; existing data kept.

-- 1) Residents: extended fields + approval workflow + relationship to head
-- (New columns appended at end. Existing rows get defaults.)
ALTER TABLE `residents`
  ADD COLUMN `place_of_birth` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `length_of_stay_years` INT DEFAULT NULL,
  ADD COLUMN `date_of_residency` DATE DEFAULT NULL,
  ADD COLUMN `email` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN `monthly_income` DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN `employment_type` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN `income_source` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `is_pwd` TINYINT(1) DEFAULT 0,
  ADD COLUMN `is_senior` TINYINT(1) DEFAULT 0,
  ADD COLUMN `sss_number` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN `philhealth_number` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN `gsis_number` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN `tin_number` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN `voter_id` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN `precinct_number` VARCHAR(20) DEFAULT NULL,
  ADD COLUMN `blood_type` VARCHAR(10) DEFAULT NULL,
  ADD COLUMN `allergies` TEXT DEFAULT NULL,
  ADD COLUMN `medical_conditions` TEXT DEFAULT NULL,
  ADD COLUMN `disability` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN `relationship_to_head` ENUM('self','spouse','child','other') DEFAULT NULL,
  ADD COLUMN `approval_status` ENUM('pending','approved','rejected') DEFAULT 'approved',
  ADD COLUMN `registration_type` ENUM('resident','official') DEFAULT 'official',
  ADD COLUMN `submitted_by_user_id` INT(11) DEFAULT NULL,
  ADD COLUMN `approved_by_user_id` INT(11) DEFAULT NULL,
  ADD COLUMN `approved_at` DATETIME DEFAULT NULL;

-- 2) Users: secretary permission to approve resident registration (assigned by captain)
ALTER TABLE `users`
  ADD COLUMN `can_approve_registration` TINYINT(1) DEFAULT 0;

-- 3) Blotters: scheduling (hearing date)
ALTER TABLE `blotters`
  ADD COLUMN `hearing_date` DATE DEFAULT NULL,
  ADD COLUMN `hearing_notes` TEXT DEFAULT NULL;

-- Index for pending resident registrations
ALTER TABLE `residents` ADD INDEX `idx_approval_status` (`approval_status`);
