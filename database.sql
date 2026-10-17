-- =======================================================
-- Database Schema: second-chance (XAMPP phpMyAdmin)
-- Matches exact phpMyAdmin table structure
-- =======================================================

CREATE DATABASE IF NOT EXISTS `second-chance`
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `second-chance`;

-- Drop table if already exists for clean import
DROP TABLE IF EXISTS `users`;

-- -------------------------------------------------------
-- Table Structure: users
-- -------------------------------------------------------
CREATE TABLE `users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fname` VARCHAR(50) NOT NULL,
    `lname` VARCHAR(50) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- Seed Initial Accounts
-- Default Passwords: Password123
-- -------------------------------------------------------

-- 1. Administrator Account (role = 'admin')
INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `password`, `phone`, `role`) VALUES
(1, 'Admin', 'Karim', 'admin@secondchance.com', '$2y$10$wN9a3a9M8N8v6f4W0wS6fe29GjXo3nK6w0k7hY1P2jZ3bK4m7mZ6e', '01011112222', 'admin');

-- 2. Student User Account (role = 'user')
INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `password`, `phone`, `role`) VALUES
(2, 'Student', 'User', 'student@secondchance.com', '$2y$10$wN9a3a9M8N8v6f4W0wS6fe29GjXo3nK6w0k7hY1P2jZ3bK4m7mZ6e', '01233334444', 'user');

ALTER TABLE `users` AUTO_INCREMENT = 3;
