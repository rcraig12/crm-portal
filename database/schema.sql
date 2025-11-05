-- CRM Portal Database Schema
-- MySQL/MariaDB Database Schema

CREATE DATABASE IF NOT EXISTS crm_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crm_portal;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'manager', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Companies table
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    industry VARCHAR(50),
    website VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    country VARCHAR(50),
    postal_code VARCHAR(20),
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_name (name),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contacts table
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    mobile VARCHAR(20),
    position VARCHAR(100),
    company_id INT,
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    country VARCHAR(50),
    postal_code VARCHAR(20),
    status ENUM('lead', 'prospect', 'customer', 'inactive') DEFAULT 'lead',
    source VARCHAR(50),
    notes TEXT,
    created_by INT,
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_name (last_name, first_name),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_company (company_id),
    INDEX idx_assigned (assigned_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Deals/Opportunities table
CREATE TABLE IF NOT EXISTS deals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    value DECIMAL(15, 2) DEFAULT 0.00,
    probability INT DEFAULT 50,
    stage ENUM('qualification', 'proposal', 'negotiation', 'closed_won', 'closed_lost') DEFAULT 'qualification',
    expected_close_date DATE,
    contact_id INT,
    company_id INT,
    created_by INT,
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE SET NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_stage (stage),
    INDEX idx_contact (contact_id),
    INDEX idx_company (company_id),
    INDEX idx_assigned (assigned_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activities table (calls, meetings, emails, etc.)
CREATE TABLE IF NOT EXISTS activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('call', 'meeting', 'email', 'task', 'note') NOT NULL,
    subject VARCHAR(200) NOT NULL,
    description TEXT,
    contact_id INT,
    company_id INT,
    deal_id INT,
    scheduled_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_by INT,
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (deal_id) REFERENCES deals(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_contact (contact_id),
    INDEX idx_deal (deal_id),
    INDEX idx_assigned (assigned_to),
    INDEX idx_scheduled (scheduled_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
-- Password is hashed using PHP password_hash()
INSERT INTO users (username, email, password, first_name, last_name, role, status)
VALUES ('admin', 'admin@crm.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', 'active');

-- Insert sample data
INSERT INTO companies (name, industry, website, phone, email, city, country, created_by)
VALUES
    ('TechCorp Solutions', 'Technology', 'https://techcorp.example.com', '+1-555-0101', 'info@techcorp.example.com', 'San Francisco', 'USA', 1),
    ('Global Retail Inc', 'Retail', 'https://globalretail.example.com', '+1-555-0102', 'contact@globalretail.example.com', 'New York', 'USA', 1),
    ('Innovate Marketing', 'Marketing', 'https://innovatemarketing.example.com', '+1-555-0103', 'hello@innovatemarketing.example.com', 'Los Angeles', 'USA', 1);

INSERT INTO contacts (first_name, last_name, email, phone, position, company_id, status, source, created_by, assigned_to)
VALUES
    ('John', 'Smith', 'john.smith@techcorp.example.com', '+1-555-1001', 'CEO', 1, 'customer', 'Website', 1, 1),
    ('Sarah', 'Johnson', 'sarah.j@globalretail.example.com', '+1-555-1002', 'Marketing Director', 2, 'prospect', 'Referral', 1, 1),
    ('Michael', 'Brown', 'mbrown@innovatemarketing.example.com', '+1-555-1003', 'Sales Manager', 3, 'lead', 'Cold Call', 1, 1);

INSERT INTO deals (title, description, value, probability, stage, expected_close_date, contact_id, company_id, created_by, assigned_to)
VALUES
    ('Enterprise Software License', 'Annual software license renewal', 50000.00, 90, 'negotiation', DATE_ADD(CURDATE(), INTERVAL 30 DAY), 1, 1, 1, 1),
    ('Marketing Campaign Q4', 'Complete marketing automation setup', 25000.00, 60, 'proposal', DATE_ADD(CURDATE(), INTERVAL 45 DAY), 2, 2, 1, 1),
    ('Consulting Services', 'Business process optimization consulting', 15000.00, 40, 'qualification', DATE_ADD(CURDATE(), INTERVAL 60 DAY), 3, 3, 1, 1);

INSERT INTO activities (type, subject, description, contact_id, status, scheduled_at, created_by, assigned_to)
VALUES
    ('call', 'Follow-up call with John Smith', 'Discuss contract renewal terms', 1, 'scheduled', DATE_ADD(NOW(), INTERVAL 2 DAY), 1, 1),
    ('meeting', 'Product demo for Sarah Johnson', 'Demonstrate new features and ROI', 2, 'scheduled', DATE_ADD(NOW(), INTERVAL 5 DAY), 1, 1),
    ('email', 'Send proposal to Michael Brown', 'Send detailed proposal and pricing', 3, 'completed', NOW(), 1, 1);
