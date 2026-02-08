# Pharma Admin - Pharmacy Product Management System

A comprehensive pharmacy product management system with advanced features including automatic image conversion to AVIF format, category-based product organization, and extensive search capabilities.

## Features

### Product Management
- ✅ Add/Edit/Delete pharmaceutical products
- ✅ Category-based organization
- ✅ Price visibility toggle (show/hide option)
- ✅ Multiple unit options (Tablet, Capsule, Syrup, ml, mg, etc.)
- ✅ Dosage information with dropdown options
- ✅ Composition/Active ingredients tracking
- ✅ Company/Manufacturer name
- ✅ Search keywords for better discoverability
- ✅ SKU (Stock Keeping Unit) management
- ✅ Stock quantity tracking
- ✅ Prescription required flag
- ✅ Featured products highlighting

### Image Management
- ✅ Automatic image conversion to AVIF format for optimal compression
- ✅ Fallback to WebP if AVIF not supported
- ✅ Automatic image resizing (max 1200x1200px)
- ✅ Multiple image format support (JPG, PNG, WEBP, GIF)
- ✅ Automatic compression while maintaining quality
- ✅ Support for multiple images per product

### Search & Filtering
- ✅ Full-text search across title, keywords, composition, and description
- ✅ Category-based filtering
- ✅ Real-time search functionality
- ✅ Pagination for large product lists

### Security Features
- ✅ CSRF token protection
- ✅ Secure session management
- ✅ Input validation and sanitization
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ Password hashing
- ✅ Login attempt tracking
- ✅ Activity logging

### Dashboard
- ✅ Product statistics
- ✅ Low stock alerts
- ✅ Category distribution
- ✅ Recent products
- ✅ Quick overview of key metrics

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.2+
- GD Library with AVIF support (PHP 8.1+ recommended for AVIF)
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

## Installation

### Step 1: Extract Files
Extract all files to your web server directory (e.g., `htdocs/pharma-admin/`)

### Step 2: Database Setup
1. Import the database schema:
   ```sql
   mysql -u root -p < pharma_database_schema.sql
   ```
   OR use phpMyAdmin to import `pharma_database_schema.sql`

2. The script will create:
   - Database: `db_pharma_admin`
   - Default admin user:
     - Username: `admin`
     - Password: `Admin@123`
   - Sample categories and products

### Step 3: Configure Database Connection
Edit `pharma_config.php` and update database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_pharma_admin');
define('DB_USER', 'your_db_user');     // Change this
define('DB_PASS', 'your_db_password'); // Change this
```

### Step 4: Set Permissions
Ensure the following directories are writable:

```bash
chmod 755 uploads/
chmod 755 uploads/products/
chmod 755 logs/
```

### Step 5: Access the System
Navigate to: `http://localhost/pharma-admin/login.php`

Login with:
- Username: `admin`
- Password: `Admin@123`

**IMPORTANT:** Change the default password immediately after first login!

## File Structure

```
pharma-admin/
├── pharma_config.php          # Configuration file
├── pharma_database_schema.sql # Database schema
├── pharma_dashboard.php       # Main dashboard
├── products.php               # Product management
├── categories.php             # Category management
├── login.php                  # Login page
├── logout.php                 # Logout handler
├── db.php                     # Database connection
├── SecureSession.php          # Session management
├── InputValidator.php         # Input validation
├── ImageConverter.php         # Image conversion utility (NEW)
├── FileUploadHandler.php      # File upload handling
├── include/
│   ├── header.php            # Header template
│   └── footer.php            # Footer template
├── assets/                   # CSS, JS, images
├── uploads/
│   └── products/             # Product images (AVIF format)
└── logs/                     # Application logs
```

## Usage Guide

### Adding a Product

1. Go to **Products** page
2. Fill in the product details:
   - **Title**: Product name (required)
   - **Category**: Select from dropdown (required)
   - **Price**: Enter product price (required)
   - **Unit**: Select unit type (required)
   - **Dosage**: Select or enter custom dosage
   - **Description**: Product description
   - **Composition**: Active ingredients
   - **Company Name**: Manufacturer
   - **Keywords**: For search optimization
   - **SKU**: Stock Keeping Unit (unique)
   - **Stock Quantity**: Current stock level

3. Upload product image (any format - will be auto-converted to AVIF)

4. Check options:
   - **Show Price**: Uncheck to hide price from users
   - **Prescription Required**: Mark if prescription needed
   - **Featured Product**: Mark to feature on homepage

5. Click **Add Product**

### Managing Categories

1. Go to **Categories** page
2. Add new category with name and description
3. View all categories with product counts
4. Delete categories (only if no products assigned)

### Image Conversion

The system automatically:
- Converts uploaded images to AVIF format
- Resizes large images to max 1200x1200px
- Maintains aspect ratio
- Compresses for optimal file size
- Falls back to WebP if AVIF not supported

Supported input formats:
- JPEG/JPG
- PNG
- WEBP
- GIF

### Search & Filter

- **Search**: Enter keywords in search box to find products
- **Category Filter**: Select category from dropdown
- **Pagination**: Navigate through multiple pages of products

### Dashboard Features

The dashboard shows:
- Total active products
- Total categories
- Low stock alerts (< 10 units)
- Out of stock items
- Prescription products count
- Featured products count
- Recent products added
- Products by category distribution

## Configuration Options

Edit `pharma_config.php` to customize:

```php
// Image settings
define('MAX_FILE_SIZE', 10485760);      // 10MB max file size
define('MAX_IMAGE_WIDTH', 1200);        // Max image width
define('MAX_IMAGE_HEIGHT', 1200);       // Max image height
define('IMAGE_QUALITY', 85);            // AVIF quality (0-100)

// Pagination
define('ITEMS_PER_PAGE', 20);           // Products per page

// Product Units (add more as needed)
define('PRODUCT_UNITS', [
    'Tablet', 'Capsule', 'Syrup', 'Injection',
    'Cream', 'Ointment', 'Drops', 'ml', 'mg', 'gm'
    // ... add more
]);

// Dosage Options (add more as needed)
define('DOSAGE_OPTIONS', [
    '1 tablet daily',
    '1 tablet twice daily',
    // ... add more
]);
```

## Database Schema

### Tables

1. **users** - Admin users and authentication
2. **categories** - Product categories
3. **products** - Main product information
4. **product_images** - Product images (supports multiple)
5. **login_attempts** - Login security tracking
6. **activity_log** - Audit trail

### Key Fields in Products Table

- `title` - Product name
- `category_id` - Foreign key to categories
- `price` - Product price (DECIMAL)
- `is_price_visible` - Show/hide price option
- `unit` - Measurement unit
- `description` - Product description
- `dosage` - Dosage instructions
- `composition` - Active ingredients
- `company_name` - Manufacturer
- `keywords` - Search keywords (comma-separated)
- `sku` - Stock Keeping Unit (unique)
- `stock_quantity` - Current stock level
- `prescription_required` - Prescription flag
- `is_active` - Active/inactive status
- `is_featured` - Featured product flag

## Security Recommendations

1. **Change Default Password**
   - Change admin password immediately after installation

2. **Database Security**
   - Create a dedicated MySQL user with limited privileges
   - Use strong passwords
   - Enable MySQL SSL connections in production

3. **File Permissions**
   - Keep config files outside web root if possible
   - Use `.htaccess` to protect sensitive files
   - Set proper file permissions (644 for files, 755 for directories)

4. **HTTPS**
   - Always use HTTPS in production
   - Set `SESSION_COOKIE_SECURE` to `true` in config

5. **Regular Updates**
   - Keep PHP and MySQL updated
   - Review security logs regularly

## Troubleshooting

### Images Not Converting to AVIF

- **Check PHP Version**: AVIF support requires PHP 8.1+
- **Check GD Library**: Run `php -i | grep -i avif` to verify
- **Fallback**: System will use WebP if AVIF not available

### Database Connection Errors

- Verify database credentials in `pharma_config.php`
- Ensure MySQL server is running
- Check database exists: `SHOW DATABASES;`

### Upload Directory Not Writable

```bash
chmod 755 uploads/
chmod 755 uploads/products/
chown www-data:www-data uploads/ -R  # Linux
```

### Low Stock Alerts Not Showing

- Check if products have `stock_quantity` set
- Verify `is_active = 1` for products

## Support & Customization

### Adding More Units

Edit `pharma_config.php`:
```php
define('PRODUCT_UNITS', [
    'Tablet', 'Capsule',
    'Your_New_Unit',  // Add here
]);
```

### Adding More Dosage Options

Edit `pharma_config.php`:
```php
define('DOSAGE_OPTIONS', [
    '1 tablet daily',
    'Your custom dosage',  // Add here
]);
```

### Customizing Image Size

Edit `pharma_config.php`:
```php
define('MAX_IMAGE_WIDTH', 1600);   // Change as needed
define('MAX_IMAGE_HEIGHT', 1600);  // Change as needed
```

## License

This software is provided as-is for pharmacy management purposes.

## Credits

- Bootstrap 5 for UI components
- Bootstrap Icons for icons
- PHP GD Library for image processing

---

For additional support or customization needs, please contact your system administrator.
