-- =====================================================
-- IT Helpdesk Ticketing System - Database Schema
-- MySQL 5.7+ / 8.0 compatible
-- =====================================================

CREATE DATABASE IF NOT EXISTS helpdesk_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE helpdesk_db;

-- Users table (both admins and regular users)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,          -- hashed with password_hash()
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Support tickets
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category ENUM('Network','Printer','Hardware','Software','Power','Account Access','Other') NOT NULL,
    priority ENUM('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    attachment VARCHAR(255) DEFAULT NULL,    -- filename in uploads/
    status ENUM('Pending','In Progress','Resolved','Closed') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Replies (from admin or user)
CREATE TABLE ticket_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,  -- 0=user, 1=admin
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Activity log for status changes and important actions
CREATE TABLE ticket_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,            -- e.g. 'status_changed', 'ticket_created', 'reply_added'
    details TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Sample data: demo users (password: "password123" for all)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),   -- password: password123
('johndoe', 'john@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('janesmith', 'jane@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Sample tickets
INSERT INTO tickets (user_id, category, priority, subject, description, status) VALUES
(2, 'Network', 'High', 'Cannot connect to VPN', 'VPN client returns error 800.', 'Pending'),
(2, 'Software', 'Medium', 'Microsoft Office crashes', 'Word crashes when opening large documents.', 'In Progress'),
(3, 'Hardware', 'Critical', 'Laptop won’t turn on', 'No power indicator, tried different outlets.', 'Pending'),
(3, 'Printer', 'Low', 'Printer queue stuck', 'Documents are stuck in queue and won’t print.', 'Resolved');

-- Sample replies
INSERT INTO ticket_replies (ticket_id, user_id, message, is_admin) VALUES
(1, 1, 'We are checking the VPN server. Please standby.', 1),
(2, 1, 'Try repairing Office from Control Panel.', 1),
(2, 2, 'I repaired but still crashes.', 0);

-- Sample activity log
INSERT INTO ticket_activity_log (ticket_id, user_id, action, details) VALUES
(1, 2, 'ticket_created', 'Ticket #1 created'),
(1, 1, 'status_changed', 'Changed status from Pending to In Progress'),
(2, 2, 'ticket_created', 'Ticket #2 created'),
(2, 1, 'reply_added', 'Admin replied to ticket #2'),
(3, 3, 'ticket_created', 'Ticket #3 created'),
(4, 3, 'ticket_created', 'Ticket #4 created'),
(4, 1, 'status_changed', 'Changed status from Pending to Resolved');