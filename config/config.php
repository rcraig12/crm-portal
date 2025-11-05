<?php
/**
 * CRM Portal Configuration File
 *
 * Configure your database connection and application settings here
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'crm_portal');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'CRM Portal');
define('APP_URL', 'http://localhost/crm-portal');
define('APP_TIMEZONE', 'UTC');

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour in seconds

// Pagination
define('RECORDS_PER_PAGE', 10);

// File Upload Configuration
define('MAX_UPLOAD_SIZE', 5242880); // 5MB in bytes
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Date Format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'M d, Y');
define('DISPLAY_DATETIME_FORMAT', 'M d, Y h:i A');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Session settings
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
