-- StudyHub v3.1 Migration Script
-- Adds missing table, indexes, and columns for performance improvements
-- Run this after backing up your database

USE studyhub_db;

-- Add deadline column if it doesn't exist
ALTER TABLE resources 
ADD COLUMN IF NOT EXISTS deadline DATE AFTER status;

-- Add performance indexes to resources table
ALTER TABLE resources 
ADD INDEX IF NOT EXISTS idx_deadline (deadline);

ALTER TABLE resources 
ADD INDEX IF NOT EXISTS idx_user_status (user_id, status);

ALTER TABLE resources 
ADD INDEX IF NOT EXISTS idx_user_deadline (user_id, deadline);

-- Create resource_tags table
CREATE TABLE IF NOT EXISTS resource_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resource_id INT NOT NULL,
    tag VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE,
    UNIQUE KEY unique_resource_tag (resource_id, tag),
    INDEX idx_resource_id (resource_id),
    INDEX idx_tag (tag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verify changes
SELECT 'Migration completed successfully!' as status;
SELECT COUNT(*) as total_resources FROM resources;
SELECT COUNT(*) as total_tags FROM resource_tags;

-- Show indexes on resources table
SHOW INDEX FROM resources;
