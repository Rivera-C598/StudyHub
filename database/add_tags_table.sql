-- Add tags support to StudyHub
-- Run this after the initial schema

USE studyhub_db;

-- Create resource_tags table
CREATE TABLE IF NOT EXISTS resource_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resource_id INT NOT NULL,
    tag VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE,
    UNIQUE KEY unique_resource_tag (resource_id, tag),
    INDEX idx_tag (tag),
    INDEX idx_resource_id (resource_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add deadline column to resources
ALTER TABLE resources 
ADD COLUMN deadline DATE NULL AFTER notes,
ADD INDEX idx_deadline (deadline);

-- Add streak tracking table
CREATE TABLE IF NOT EXISTS user_streaks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    resources_completed INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, date),
    INDEX idx_user_date (user_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
