-- ====================================================================
-- Database Schema for SmartService AI
-- Comprehensive Database Setup with Foreign Keys & Seed Data
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `smartservice_ai` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `smartservice_ai`;

-- Disable Foreign Key Checks for clean table drop/recreation
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `ai_consultations`;
DROP TABLE IF EXISTS `service_requests`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `ai_configurations`;
DROP TABLE IF EXISTS `otp_verifications`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------------------
-- 1. Table `users`
-- --------------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'customer', 'technician') NOT NULL DEFAULT 'customer',
  `phone` VARCHAR(20) DEFAULT NULL,
  `phone_verified_at` DATETIME DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_role` (`role`),
  INDEX `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 2. Table `categories`
-- --------------------------------------------------------------------
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 3. Table `services`
-- --------------------------------------------------------------------
CREATE TABLE `services` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `duration` VARCHAR(50) DEFAULT NULL COMMENT 'Estimasi durasi pengerjaan, contoh: 60 menit',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_services_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_services_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 4. Table `service_requests`
-- --------------------------------------------------------------------
CREATE TABLE `service_requests` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT UNSIGNED NOT NULL,
  `technician_id` INT UNSIGNED DEFAULT NULL,
  `ai_consultation_id` INT UNSIGNED DEFAULT NULL,
  `service_id` INT UNSIGNED NOT NULL,
  `complaint` TEXT NOT NULL,
  `complaint_photo` VARCHAR(255) DEFAULT NULL COMMENT 'Foto kerusakan yang diupload customer saat booking',
  `address` TEXT NOT NULL,
  `schedule` DATETIME NOT NULL,
  `status` ENUM('pending', 'assigned', 'accepted', 'on_the_way', 'repairing', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `estimated_price` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `final_price` DECIMAL(12, 2) DEFAULT NULL,
  `technician_note` TEXT DEFAULT NULL,
  `inspection_result` TEXT DEFAULT NULL,
  `repair_action` TEXT DEFAULT NULL,
  `service_fee` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `sparepart_fee` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `completion_photo` VARCHAR(255) DEFAULT NULL,
  `rejection_note` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_service_requests_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_requests_technician` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_service_requests_ai` FOREIGN KEY (`ai_consultation_id`) REFERENCES `ai_consultations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_service_requests_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_service_requests_status` (`status`),
  INDEX `idx_service_requests_customer` (`customer_id`),
  INDEX `idx_service_requests_technician` (`technician_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 5. Table `ai_consultations`
-- --------------------------------------------------------------------
CREATE TABLE `ai_consultations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `question` TEXT NOT NULL,
  `answer` TEXT NOT NULL,
  `ai_result` JSON DEFAULT NULL COMMENT 'Hasil analisis AI terstruktur dalam format JSON',
  `recommendation` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_ai_consultations_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ai_consultations_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_ai_consultations_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 6. Table `reviews`
-- --------------------------------------------------------------------
CREATE TABLE `reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_request_id` INT UNSIGNED NOT NULL UNIQUE,
  `customer_id` INT UNSIGNED NOT NULL,
  `technician_id` INT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL COMMENT 'Skala rating 1 - 5',
  `comment` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reviews_service_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_technician` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_reviews_technician` (`technician_id`),
  INDEX `idx_reviews_rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 7. Table `payments`
-- --------------------------------------------------------------------
CREATE TABLE `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_request_id` INT UNSIGNED NOT NULL UNIQUE,
  `amount` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
  `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
  `payment_method` VARCHAR(50) NOT NULL DEFAULT 'cash' COMMENT 'cash, qris, transfer_bank, e-wallet',
  `paid_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_payments_service_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_payments_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 8. Table `status_logs`
-- --------------------------------------------------------------------
CREATE TABLE `status_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_request_id` INT UNSIGNED NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `note` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_status_logs_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_status_logs_request` (`service_request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 9. Table `otp_verifications`
-- --------------------------------------------------------------------
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

-- --------------------------------------------------------------------
-- 10. Table `ai_configurations`
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ai_configurations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `provider` VARCHAR(50) NOT NULL DEFAULT 'groq',
  `model_id` VARCHAR(100) NOT NULL DEFAULT 'qwen/qwen3.8-27b',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_by` INT UNSIGNED DEFAULT NULL,
  `updated_by_name` VARCHAR(100) DEFAULT NULL,
  `last_tested_at` DATETIME DEFAULT NULL,
  `last_test_status` ENUM('success', 'failed') DEFAULT NULL,
  `last_test_latency` INT UNSIGNED DEFAULT NULL COMMENT 'Latency in ms',
  `last_test_message` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_ai_provider` (`provider`),
  INDEX `idx_ai_active` (`is_active`),
  CONSTRAINT `fk_ai_config_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- SEED DATA INITIALIZATION
-- ====================================================================

-- Seed Users (Password default: 'password123' ter-hash dengan password_hash)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `address`, `status`) VALUES
(1, 'System Administrator', 'admin@smartservice.ai', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1055055055055055055055055055055', 'admin', '081200000001', 'HQ SmartService AI, Jakarta', 'active'),
(2, 'Budi Santoso (Customer)', 'budi.customer@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1055055055055055055055055055055', 'customer', '081298765432', 'Jl. Sudirman No. 45, Jakarta Selatan', 'active'),
(3, 'Ahmad Hidayat (Teknisi AC)', 'ahmad.teknisi@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1055055055055055055055055055055', 'technician', '081311223344', 'Jl. Kebon Jeruk No. 12, Jakarta Barat', 'active'),
(4, 'Siti Nurhaliza (Customer)', 'siti.customer@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1055055055055055055055055055055', 'customer', '081512345678', 'Jl. Gatot Subroto No. 88, Jakarta Selatan', 'active'),
(5, 'Rudi Hermawan (Teknisi Kulkas/Elektronik)', 'rudi.teknisi@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1055055055055055055055055055055', 'technician', '081799887766', 'Jl. Tebet Raya No. 10, Jakarta Selatan', 'active');

-- Seed Categories
INSERT INTO `categories` (`id`, `name`, `description`, `status`) VALUES
(1, 'AC', 'Layanan perbaikan, pembersihan (cuci), cuci besar, dan isi freon Air Conditioner.', 'active'),
(2, 'Kulkas', 'Layanan perbaikan kulkas 1/2 pintu, freezer box, ganti kompresor, dan kebocoran freon.', 'active'),
(3, 'Mesin Cuci', 'Perbaikan mesin cuci 1 tabung (top/front loading) dan 2 tabung, masalah pengering & modul.', 'active'),
(4, 'TV', 'Perbaikan TV LED, OLED, Smart TV, layar bergaris, mati total, atau tidak ada suara.', 'active'),
(5, 'Dispenser', 'Perbaikan dispenser air minum panas/dingin, bocor, atau kelistrikan.', 'active'),
(6, 'Kompor', 'Perbaikan kompor gas freestanding, kompor tanam, dan kompor induksi.', 'active'),
(7, 'Peralatan Lainnya', 'Perbaikan pompa air, microwave, blender, kipas angin, dan peralatan rumah tangga lainnya.', 'active');

-- Seed Services
INSERT INTO `services` (`id`, `category_id`, `name`, `description`, `price`, `duration`, `status`) VALUES
(1, 1, 'Cuci AC Standard (0.5 - 1 PK)', 'Pembersihan evaporator, filter air, dan unit outdoor AC standard.', 75000.00, '45 menit', 'active'),
(2, 1, 'Cuci AC Inverter (1.5 - 2 PK)', 'Pembersihan menyeluruh unit AC inverter daya besar.', 95000.00, '60 menit', 'active'),
(3, 1, 'Tambah Freon R32 / R410a', 'Pengisian ulang gas freon eco-friendly R32 atau R410a.', 150000.00, '30 menit', 'active'),
(4, 2, 'Servis Kulkas Tidak Dingin', 'Pengecekan thermostat, relay, overload, dan pembersihan kondensor.', 120000.00, '60 menit', 'active'),
(5, 2, 'Ganti Kompresor Kulkas', 'Penggantian unit kompresor kulkas baru beserta pengisian freon.', 450000.00, '120 menit', 'active'),
(6, 3, 'Servis Mesin Cuci Air Tidak Keluar', 'Pembersihan katup drain valve dan pengecekan motor pembuangan.', 110000.00, '45 menit', 'active'),
(7, 3, 'Ganti Modul Control Mesin Cuci', 'Penggantian PCB mainboard mesin cuci otomatis.', 250000.00, '90 menit', 'active'),
(8, 4, 'Servis TV Mati Total / Power Supply', 'Perbaikan atau penggantian komponen modul power supply TV LED.', 180000.00, '90 menit', 'active'),
(9, 5, 'Servis Dispenser Air Tidak Panas/Dingin', 'Penggantian element pemanas / peltier pendingin dispenser.', 95000.00, '45 menit', 'active'),
(10, 6, 'Perbaikan Kompor Gas Pemantik Mati', 'Pembersihan burner head dan penggantian kabel pemantik kompor.', 85000.00, '40 menit', 'active');

-- Seed Sample Service Requests
INSERT INTO `service_requests` (`id`, `customer_id`, `technician_id`, `service_id`, `complaint`, `address`, `schedule`, `status`, `estimated_price`, `final_price`, `technician_note`) VALUES
(1, 2, 3, 1, 'AC kamar utama kurang dingin dan berbunyi bising.', 'Jl. Sudirman No. 45, Jakarta Selatan', '2026-09-05 10:00:00', 'completed', 75000.00, 75000.00, 'AC telah dicuci bersih, filter disemprot, freon masih cukup.'),
(2, 4, 5, 4, 'Kulkas bagian bawah tidak dingin, bagian atas pembeku normal.', 'Jl. Gatot Subroto No. 88, Jakarta Selatan', '2026-09-06 14:00:00', 'repairing', 120000.00, NULL, 'Sedang pengecekan timern defroster dan fan motor evaporator.');

-- Seed Sample Payment for Completed Request
INSERT INTO `payments` (`id`, `service_request_id`, `amount`, `payment_status`, `payment_method`, `paid_at`) VALUES
(1, 1, 75000.00, 'paid', 'qris', '2026-09-05 11:15:00');

-- Seed Sample Review for Completed Request
INSERT INTO `reviews` (`id`, `service_request_id`, `customer_id`, `technician_id`, `rating`, `comment`) VALUES
(1, 1, 2, 3, 5, 'Teknisi Ahmad sangat ramah dan pengerjaan cuci AC rapi sekali! AC kembali dingin sejuk.');

-- Seed AI Model Configuration (Groq Active Model)
INSERT INTO `ai_configurations` (`id`, `provider`, `model_id`, `is_active`, `updated_by_name`, `last_test_status`, `last_test_message`, `last_tested_at`) VALUES
(1, 'groq', 'qwen/qwen3.8-27b', 1, 'System Default', 'success', 'Model terverifikasi aktif pada pengujian awal', NOW());
