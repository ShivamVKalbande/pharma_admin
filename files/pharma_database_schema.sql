-- Secure Database Schema for Pharma Admin
-- Run this script to create/update your database

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS db_pharma_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_pharma_admin;

-- Drop existing tables if they exist (CAUTION: This will delete data!)
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `activity_log`;

-- User table with secure password storage
CREATE TABLE `user` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL COMMENT 'Password hash using password_hash()',
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `full_name` VARCHAR(100),
    `role` ENUM('admin', 'manager', 'user') DEFAULT 'user',
    `status` ENUM('active', 'inactive', 'locked') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    `password_changed_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_username` (`username`),
    INDEX `idx_email` (`email`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table for product categorization
CREATE TABLE `categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `is_active` TINYINT(1) DEFAULT 1,
    `display_order` INT(11) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_slug` (`slug`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products table for pharmacy products
CREATE TABLE `products` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `category_id` INT(11) NOT NULL,
    `price` DECIMAL(12,2) NOT NULL,
    `is_price_visible` TINYINT(1) DEFAULT 1 COMMENT '1=visible, 0=hidden',
    `unit` VARCHAR(50) NOT NULL COMMENT 'e.g., Tablet, Capsule, Syrup, ml, mg',
    `description` TEXT,
    `dosage` VARCHAR(100) COMMENT 'e.g., 500mg, 10ml, 1 tablet twice daily',
    `composition` TEXT COMMENT 'Active ingredients and composition',
    `company_name` VARCHAR(200) COMMENT 'Manufacturer/Company name',
    `keywords` TEXT COMMENT 'Search keywords (comma-separated)',
    `sku` VARCHAR(50) UNIQUE COMMENT 'Stock Keeping Unit',
    `stock_quantity` INT(11) DEFAULT 0,
    `prescription_required` TINYINT(1) DEFAULT 0 COMMENT '1=prescription required, 0=OTC',
    `is_active` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_category_id` (`category_id`),
    INDEX `idx_title` (`title`),
    INDEX `idx_price` (`price`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_is_featured` (`is_featured`),
    INDEX `idx_sku` (`sku`),
    FULLTEXT INDEX `idx_search` (`title`, `keywords`, `composition`, `description`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`created_by`) REFERENCES `user`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product images table (supports multiple images per product)
CREATE TABLE `product_images` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `product_id` INT(11) NOT NULL,
    `image_name` VARCHAR(255) NOT NULL COMMENT 'Stored in AVIF format',
    `original_name` VARCHAR(255),
    `is_primary` TINYINT(1) DEFAULT 0,
    `display_order` INT(11) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_product_id` (`product_id`),
    INDEX `idx_is_primary` (`is_primary`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login attempts table for rate limiting
CREATE TABLE `login_attempts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `attempt_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `success` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX `idx_username_time` (`username`, `attempt_time`),
    INDEX `idx_ip_time` (`ip_address`, `attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity log table for audit trail
CREATE TABLE `activity_log` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `table_name` VARCHAR(50),
    `record_id` INT(11),
    `description` TEXT,
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created_at` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
-- Default password: Admin@123 (PLEASE CHANGE THIS IMMEDIATELY!)
INSERT INTO `user` (`username`, `password`, `email`, `full_name`, `role`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@pharma.com', 'Administrator', 'admin', 'active');

-- Insert sample categories
INSERT INTO `categories` (`name`, `description`, `slug`, `is_active`, `display_order`) VALUES
('Pain Relief', 'Medications for pain management', 'pain-relief', 1, 1),
('Antibiotics', 'Antibacterial medications', 'antibiotics', 1, 2),
('Vitamins & Supplements', 'Nutritional supplements and vitamins', 'vitamins-supplements', 1, 3),
('Cardiac Care', 'Heart and cardiovascular medications', 'cardiac-care', 1, 4),
('Diabetes Care', 'Medications for diabetes management', 'diabetes-care', 1, 5),
('Cold & Flu', 'Cold, cough and flu medications', 'cold-flu', 1, 6),
('Digestive Health', 'Medications for digestive system', 'digestive-health', 1, 7),
('Skin Care', 'Topical medications and skin treatments', 'skin-care', 1, 8),
('Respiratory', 'Respiratory system medications', 'respiratory', 1, 9),
('General Medicine', 'General healthcare products', 'general-medicine', 1, 10);

-- Insert sample products
INSERT INTO `products` (
    `title`, `category_id`, `price`, `is_price_visible`, `unit`, `description`, 
    `dosage`, `composition`, `company_name`, `keywords`, `sku`, `stock_quantity`, `prescription_required`
) VALUES
('Paracetamol 500mg', 1, 25.50, 1, 'Tablet', 'Fast-acting pain reliever and fever reducer', '1-2 tablets every 4-6 hours', 'Paracetamol 500mg', 'PharmaCorp Ltd', 'paracetamol, fever, headache, pain relief', 'MED001', 500, 0),
('Amoxicillin 250mg', 2, 120.00, 1, 'Capsule', 'Broad-spectrum antibiotic', '1 capsule 3 times daily', 'Amoxicillin Trihydrate 250mg', 'MediCare Pharma', 'antibiotic, infection, bacterial', 'MED002', 200, 1),
('Vitamin D3 1000IU', 3, 199.00, 1, 'Tablet', 'Bone health and immunity support', '1 tablet daily', 'Cholecalciferol 1000 IU', 'HealthPlus', 'vitamin d, bone health, immunity', 'MED003', 350, 0),
('Cough Syrup', 6, 85.00, 1, 'Bottle (100ml)', 'Relieves dry and wet cough', '10ml three times daily', 'Dextromethorphan 10mg/5ml', 'WellCare', 'cough, cold, syrup', 'MED004', 150, 0);

-- Create view for product statistics
CREATE OR REPLACE VIEW `product_statistics` AS
SELECT 
    c.name as category_name,
    COUNT(p.id) as total_products,
    AVG(p.price) as avg_price,
    MIN(p.price) as min_price,
    MAX(p.price) as max_price,
    SUM(p.stock_quantity) as total_stock
FROM categories c
LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
GROUP BY c.id, c.name;
