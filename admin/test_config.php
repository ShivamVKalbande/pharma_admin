<?php
// Debug Test File - Check Pharma Admin Configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Pharma Admin Debug Test</h2>";
echo "<hr>";

// Test 1: Check if files exist
echo "<h3>1. File Existence Check</h3>";
$files = [
    'pharma_config.php',
    'db.php',
    'SecureSession.php',
    'InputValidator.php',
    'ImageConverter.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file - EXISTS<br>";
    } else {
        echo "❌ $file - MISSING<br>";
    }
}
echo "<hr>";

// Test 2: Load config
echo "<h3>2. Configuration Test</h3>";
try {
    require_once 'pharma_config.php';
    echo "✅ pharma_config.php loaded successfully<br>";
    echo "Database: " . DB_NAME . "<br>";
    echo "Host: " . DB_HOST . "<br>";
    echo "User: " . DB_USER . "<br>";
} catch (Exception $e) {
    echo "❌ Error loading config: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 3: Database connection
echo "<h3>3. Database Connection Test</h3>";
try {
    require_once 'db.php';
    $db = getDB();
    echo "✅ Database connection successful<br>";
    
    // Test query
    $stmt = $db->query("SELECT DATABASE() as dbname");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Connected to database: " . $result['dbname'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    echo "<br><strong>Possible solutions:</strong><br>";
    echo "1. Check if MySQL/MariaDB is running<br>";
    echo "2. Verify database credentials in pharma_config.php<br>";
    echo "3. Make sure database 'db_pharma_admin' exists<br>";
    echo "4. Run: mysql -u root -p < pharma_database_schema.sql<br>";
}
echo "<hr>";

// Test 4: Check tables
echo "<h3>4. Database Tables Check</h3>";
if (isset($db)) {
    try {
        $tables = ['user', 'categories', 'products', 'product_images'];
        foreach ($tables as $table) {
            $stmt = $db->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Table '$table' exists<br>";
                
                // Count records
                $countStmt = $db->query("SELECT COUNT(*) as count FROM $table");
                $count = $countStmt->fetch(PDO::FETCH_ASSOC);
                echo "&nbsp;&nbsp;&nbsp;&nbsp;Records: " . $count['count'] . "<br>";
            } else {
                echo "❌ Table '$table' does NOT exist<br>";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error checking tables: " . $e->getMessage() . "<br>";
    }
}
echo "<hr>";

// Test 5: Session test
echo "<h3>5. Session Test</h3>";
try {
    require_once 'SecureSession.php';
    SecureSession::init();
    echo "✅ Session initialized successfully<br>";
    echo "Session ID: " . session_id() . "<br>";
    
    if (isset($_SESSION['user_id'])) {
        echo "✅ User logged in (ID: " . $_SESSION['user_id'] . ")<br>";
    } else {
        echo "⚠️ No user logged in<br>";
    }
} catch (Exception $e) {
    echo "❌ Session error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 6: Check upload directory
echo "<h3>6. Upload Directory Check</h3>";
$uploadDir = 'uploads/products/';
if (is_dir($uploadDir)) {
    echo "✅ Upload directory exists: $uploadDir<br>";
    if (is_writable($uploadDir)) {
        echo "✅ Upload directory is writable<br>";
    } else {
        echo "❌ Upload directory is NOT writable<br>";
        echo "Run: chmod 755 $uploadDir<br>";
    }
} else {
    echo "❌ Upload directory does NOT exist<br>";
    echo "Creating directory...<br>";
    if (mkdir($uploadDir, 0755, true)) {
        echo "✅ Directory created successfully<br>";
    } else {
        echo "❌ Failed to create directory<br>";
    }
}
echo "<hr>";

// Test 7: PHP version and extensions
echo "<h3>7. PHP Environment</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "PDO: " . (extension_loaded('pdo') ? '✅ Enabled' : '❌ Disabled') . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Enabled' : '❌ Disabled') . "<br>";
echo "GD: " . (extension_loaded('gd') ? '✅ Enabled' : '❌ Disabled') . "<br>";
echo "Session: " . (extension_loaded('session') ? '✅ Enabled' : '❌ Disabled') . "<br>";

if (function_exists('imageavif')) {
    echo "AVIF Support: ✅ Enabled (PHP 8.1+)<br>";
} else {
    echo "AVIF Support: ⚠️ Disabled (will use WebP fallback)<br>";
}
echo "<hr>";

echo "<h3>Summary</h3>";
echo "<p>If all tests pass, your Pharma Admin should work correctly.</p>";
echo "<p>If you see errors, fix them in the order shown above.</p>";
echo "<br>";
echo "<a href='login.php' class='btn btn-primary' style='padding: 10px 20px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px;'>Go to Login</a> ";
echo "<a href='product_list.php' class='btn btn-success' style='padding: 10px 20px; background: #198754; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Go to Products</a>";
?>
