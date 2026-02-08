<?php
/**
 * Pharma Admin Configuration File
 * Contains all configuration settings for the application
 * 
 * SECURITY NOTE: Keep this file outside the web root in production
 * Or ensure it's protected by .htaccess
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php-error.log');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_pharma_admin');
define('DB_USER', 'root'); // Change in production
define('DB_PASS', ''); // Change in production
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'Pharma Admin');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/pharma-admin/');

// Session Configuration
define('SESSION_NAME', 'PHARMA_ADMIN_SESSION');
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_COOKIE_SECURE', false); // Set to true in production with HTTPS
define('SESSION_COOKIE_SAMESITE', 'Strict');

// Security Settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LENGTH', 32);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Upload Settings
define('UPLOAD_DIR', __DIR__ . '/uploads/products/');
define('MAX_FILE_SIZE', 10485760); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif']);
define('MAX_IMAGE_WIDTH', 1200);
define('MAX_IMAGE_HEIGHT', 1200);
define('IMAGE_QUALITY', 85);

// Pagination
define('ITEMS_PER_PAGE', 20);

// Product Units (Dropdown options)
define('PRODUCT_UNITS', [
    'Tablet',
    'Capsule',
    'Syrup',
    'Injection',
    'Cream',
    'Ointment',
    'Drops',
    'Suspension',
    'Powder',
    'Gel',
    'Lotion',
    'Spray',
    'Inhaler',
    'ml',
    'mg',
    'gm',
    'mcg',
    'IU',
    'Strip',
    'Box',
    'Bottle',
    'Tube',
    'Vial'
]);

// Dosage Options (Dropdown)
define('DOSAGE_OPTIONS', [
    '1 tablet daily',
    '2 tablets daily',
    '1 tablet twice daily',
    '1 tablet three times daily',
    '1 capsule daily',
    '2 capsules daily',
    '1 capsule twice daily',
    '5ml twice daily',
    '10ml twice daily',
    '5ml three times daily',
    '10ml three times daily',
    'As directed by physician',
    'Apply twice daily',
    'Apply as needed',
    '1-2 drops in affected eye',
    'Custom dosage'
]);

// Status Messages
define('MSG_SUCCESS_ADD', 'Product added successfully!');
define('MSG_SUCCESS_UPDATE', 'Product updated successfully!');
define('MSG_SUCCESS_DELETE', 'Product deleted successfully!');
define('MSG_ERROR_GENERIC', 'An error occurred. Please try again.');
define('MSG_ERROR_UNAUTHORIZED', 'Unauthorized access.');

// Log file paths
define('LOG_DIR', __DIR__ . '/logs/');
define('ERROR_LOG', LOG_DIR . 'error.log');
define('ACTIVITY_LOG', LOG_DIR . 'activity.log');

// Create necessary directories
$directories = [
    UPLOAD_DIR,
    LOG_DIR
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Autoload classes (simple autoloader)
spl_autoload_register(function($className) {
    $file = __DIR__ . '/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
