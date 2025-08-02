-- Database Alterations for Fees Management System

-- 1. Add fees column to students table
ALTER TABLE `students` ADD COLUMN `fees` DECIMAL(10,2) DEFAULT 0.00 AFTER `state`;

-- 2. Drop existing fee_record table and recreate with new structure
DROP TABLE IF EXISTS `fee_record`;

CREATE TABLE `fee_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(40) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_date` DATE NOT NULL,
  `payment_mode` ENUM('cash', 'upi', 'cheque') NOT NULL,
  `collected_by_owner` BOOLEAN DEFAULT FALSE,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Timetable images for class/section
CREATE TABLE IF NOT EXISTS timetable_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  class VARCHAR(20) NOT NULL,
  section VARCHAR(5) NOT NULL,
  filename VARCHAR(255) NOT NULL,
  uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Add some sample data for testing
INSERT INTO `fee_record` (`student_id`, `amount`, `payment_date`, `payment_mode`, `collected_by_owner`) VALUES
('S1746314678', 5000.00, '2025-01-15', 'cash', FALSE),
('S1746314845', 3000.00, '2025-01-20', 'upi', TRUE),
('S1746315055', 2500.00, '2025-01-25', 'cheque', FALSE),
('S1746315238', 4000.00, '2025-02-01', 'cash', TRUE),
('S1746315403', 3500.00, '2025-02-05', 'upi', FALSE);

-- 4. Update some students with sample fees
UPDATE `students` SET `fees` = 20000.00 WHERE `id` = 'S1746314678';
UPDATE `students` SET `fees` = 25000.00 WHERE `id` = 'S1746314845';
UPDATE `students` SET `fees` = 22000.00 WHERE `id` = 'S1746315055';
UPDATE `students` SET `fees` = 28000.00 WHERE `id` = 'S1746315238';
UPDATE `students` SET `fees` = 30000.00 WHERE `id` = 'S1746315403'; 

ALTER TABLE fee_record ADD COLUMN description VARCHAR(255) DEFAULT NULL;