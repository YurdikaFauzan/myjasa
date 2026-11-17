-- ====================================================================
-- Migration: Add OTP Verifications Table & Phone Verified Column
-- SmartService AI - WhatsApp Fonte OTP Authentication
-- ====================================================================

USE `smartservice_ai`;

-- 1. Tambah kolom phone_verified_at pada tabel users (jika belum ada)
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'users' 
  AND COLUMN_NAME = 'phone_verified_at';

SET @query = IF(@col_exists = 0, 
  'ALTER TABLE `users` ADD COLUMN `phone_verified_at` DATETIME DEFAULT NULL AFTER `phone`', 
  'SELECT "Column phone_verified_at already exists"');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Buat tabel otp_verifications untuk mencatat sesi OTP Fonte secara aman
CREATE TABLE IF NOT EXISTS `otp_verifications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `phone` VARCHAR(20) NOT NULL,
  `otp_hash` VARCHAR(255) NOT NULL,
  `action` ENUM('login', 'register', 'verification') NOT NULL DEFAULT 'login',
  `user_id` INT UNSIGNED DEFAULT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `max_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('pending', 'verified', 'expired', 'failed') NOT NULL DEFAULT 'pending',
  `expires_at` DATETIME NOT NULL,
  `verified_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_otp_phone` (`phone`),
  INDEX `idx_otp_status` (`status`),
  INDEX `idx_otp_expires` (`expires_at`),
  INDEX `idx_otp_created` (`created_at`),
  INDEX `idx_otp_user` (`user_id`),
  CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
