# Troubleshooting HTTP 500 Error - Pharma Admin

## Quick Fix Steps

### Step 1: Run the Debug Test
Navigate to: `http://localhost/urban_nest/admin/test_config.php`

This will show you exactly what's wrong.

### Step 2: Common Issues & Solutions

#### Issue 1: Database Not Created
**Symptoms:** "Database connection failed" or "Unknown database"

**Solution:**
```sql
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE db_pharma_admin;

# Import schema
USE db_pharma_admin;
SOURCE pharma_database_schema.sql;

# Or from command line:
mysql -u root -p < pharma_database_schema.sql
```

#### Issue 2: Wrong File Names
**Symptoms:** "Failed to open stream" or "No such file"

**Check:** Make sure you're using the NEW files:
- ✅ Use: `product_list.php` (NEW)
- ❌ Don't use: `products.php` (OLD)
- ✅ Use: `add_product.php` (NEW)

#### Issue 3: Missing pharma_config.php
**Symptoms:** "pharma_config.php not found"

**Solution:**
Make sure `pharma_config.php` exists in the same directory as product_list.php

#### Issue 4: Database Credentials Wrong
**Symptoms:** "Access denied for user"

**Solution:**
Edit `pharma_config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_pharma_admin');
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', '');            // Your MySQL password
```

#### Issue 5: PHP Error Logs
**Check logs:**
- Look in: `logs/php-error.log`
- Look in: `logs/product_list_errors.log`
- Apache error log: `/var/log/apache2/error.log` (Linux)
- XAMPP: `xampp/apache/logs/error.log`

### Step 3: Enable Error Display

Add this to the TOP of `product_list.php`:
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Step 4: Check Apache/PHP

**For XAMPP:**
- Make sure Apache is running
- Make sure MySQL is running

**For Linux:**
```bash
sudo service apache2 restart
sudo service mysql restart
```

### Step 5: File Permissions

```bash
chmod 644 *.php
chmod 755 uploads/
chmod 755 uploads/products/
chmod 755 logs/
```

### Step 6: Check .htaccess

If you have a `.htaccess` file, make sure it's not blocking PHP execution.

## Testing URL Structure

**Correct URLs:**
- ✅ `http://localhost/urban_nest/admin/product_list.php`
- ✅ `http://localhost/urban_nest/admin/add_product.php`
- ✅ `http://localhost/urban_nest/admin/test_config.php`

**Wrong (old files):**
- ❌ `http://localhost/urban_nest/admin/products.php`

## Most Common HTTP 500 Causes

1. **Database not created** (60% of cases)
   - Solution: Import `pharma_database_schema.sql`

2. **Wrong database credentials** (20% of cases)
   - Solution: Update `pharma_config.php`

3. **PHP syntax error** (10% of cases)
   - Solution: Check error logs

4. **Missing required files** (5% of cases)
   - Solution: Make sure all files are uploaded

5. **File permissions** (5% of cases)
   - Solution: Set correct chmod permissions

## Quick Diagnostic Commands

```bash
# Check if database exists
mysql -u root -p -e "SHOW DATABASES LIKE 'db_pharma_admin';"

# Check tables
mysql -u root -p db_pharma_admin -e "SHOW TABLES;"

# Check categories data
mysql -u root -p db_pharma_admin -e "SELECT * FROM categories;"

# Check PHP syntax
php -l product_list.php

# Check Apache error log (last 20 lines)
tail -20 /var/log/apache2/error.log
```

## Still Not Working?

1. **Access test_config.php first**
   - `http://localhost/urban_nest/admin/test_config.php`
   - This will tell you EXACTLY what's wrong

2. **Check the exact error**
   - Look in browser developer console (F12)
   - Look in Apache error logs
   - Look in PHP error logs

3. **Verify file structure**
   ```
   admin/
   ├── pharma_config.php      ← Must exist
   ├── db.php                 ← Must exist
   ├── product_list.php       ← NEW file
   ├── add_product.php        ← NEW file
   ├── search_suggestions.php ← NEW file
   ├── get_product_details.php← NEW file
   ├── test_config.php        ← Debug file
   └── ...
   ```

## Need More Help?

Run this command and share the output:
```bash
php test_config.php
```

Or check the specific error in:
- `logs/product_list_errors.log`
- Browser console (F12 → Network tab → Click on product_list.php → Preview)

---

**Remember:** The most common issue is that the database hasn't been created or imported yet!
