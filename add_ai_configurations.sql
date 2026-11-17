-- ====================================================================
-- Migration: Add AI Configurations Table (Multi-Provider Support)
-- SmartService AI - AI Model Configuration Management
-- Providers: Groq, OpenAI, Google Gemini, DeepSeek
-- ====================================================================

USE `smartservice_ai`;

CREATE TABLE IF NOT EXISTS `ai_configurations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `provider` VARCHAR(50) NOT NULL DEFAULT 'groq',
  `model_id` VARCHAR(100) NOT NULL DEFAULT 'qwen/qwen3.8-27b',
  `api_key` TEXT DEFAULT NULL COMMENT 'Encrypted/protected provider API Key',
  `base_url` VARCHAR(255) DEFAULT NULL COMMENT 'API Base URL / Endpoint',
  `temperature` DECIMAL(3,2) NOT NULL DEFAULT 0.50 COMMENT 'Model Temperature (0.0 - 2.0)',
  `max_tokens` INT UNSIGNED NOT NULL DEFAULT 650 COMMENT 'Max completion tokens',
  `timeout` INT UNSIGNED NOT NULL DEFAULT 20 COMMENT 'HTTP timeout in seconds',
  `is_active` TINYINT(1) NOT NULL DEFAULT 0,
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

-- Insert default configurations for providers if not already present
INSERT INTO `ai_configurations` (`id`, `provider`, `model_id`, `base_url`, `temperature`, `max_tokens`, `timeout`, `is_active`, `updated_by_name`, `last_test_status`, `last_test_message`, `last_tested_at`)
SELECT 1, 'groq', 'qwen/qwen3.8-27b', 'https://api.groq.com/openai/v1', 0.50, 650, 20, 1, 'System Default', 'success', 'Model terverifikasi aktif pada pengujian awal', NOW()
WHERE NOT EXISTS (SELECT 1 FROM `ai_configurations` WHERE `provider` = 'groq');

INSERT INTO `ai_configurations` (`provider`, `model_id`, `base_url`, `temperature`, `max_tokens`, `timeout`, `is_active`, `updated_by_name`, `created_at`, `updated_at`)
SELECT 'openai', 'gpt-4o-mini', 'https://api.openai.com/v1', 0.70, 700, 25, 0, 'System Default', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `ai_configurations` WHERE `provider` = 'openai');

INSERT INTO `ai_configurations` (`provider`, `model_id`, `base_url`, `temperature`, `max_tokens`, `timeout`, `is_active`, `updated_by_name`, `created_at`, `updated_at`)
SELECT 'gemini', 'gemini-1.5-flash', 'https://generativelanguage.googleapis.com/v1beta/openai', 0.50, 700, 25, 0, 'System Default', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `ai_configurations` WHERE `provider` = 'gemini');

INSERT INTO `ai_configurations` (`provider`, `model_id`, `base_url`, `temperature`, `max_tokens`, `timeout`, `is_active`, `updated_by_name`, `created_at`, `updated_at`)
SELECT 'deepseek', 'deepseek-chat', 'https://api.deepseek.com', 0.70, 700, 25, 0, 'System Default', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `ai_configurations` WHERE `provider` = 'deepseek');
