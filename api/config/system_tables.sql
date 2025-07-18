-- System logs table for the enhanced admin settings
-- This table stores system activity logs for monitoring and debugging

CREATE TABLE IF NOT EXISTS `system_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_type` enum('info','warning','error','success','system','export') NOT NULL DEFAULT 'info',
  `message` text NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_log_type` (`log_type`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_admin_id` (`admin_id`),
  CONSTRAINT `fk_system_logs_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- System configuration table for storing system-wide settings
CREATE TABLE IF NOT EXISTS `system_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL UNIQUE,
  `config_value` text,
  `description` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `idx_config_key` (`config_key`),
  KEY `idx_updated_by` (`updated_by`),
  CONSTRAINT `fk_system_config_admin` FOREIGN KEY (`updated_by`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default system configuration values
INSERT IGNORE INTO `system_config` (`config_key`, `config_value`, `description`) VALUES
('system_name', 'Exam Management System', 'The name of the system'),
('system_description', 'A comprehensive exam management system for educational institutions', 'Description of the system'),
('maintenance_mode', '0', 'Whether the system is in maintenance mode (1=enabled, 0=disabled)'),
('enable_notifications', '1', 'Whether notifications are enabled system-wide'),
('session_timeout', '3600', 'Session timeout in seconds'),
('max_login_attempts', '5', 'Maximum login attempts before account lockout'),
('email_notifications', '1', 'Whether email notifications are enabled'),
('system_timezone', 'UTC', 'Default system timezone');

-- Sample log entries for testing
INSERT IGNORE INTO `system_logs` (`log_type`, `message`, `admin_id`) VALUES
('system', 'System configuration updated', NULL),
('info', 'Admin settings page accessed', NULL),
('success', 'Database optimization completed', NULL),
('warning', 'High memory usage detected', NULL);
