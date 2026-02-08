# Quick Installation Guide - Pharma Admin

## What's New in Pharma Admin

This is an upgraded version of Urban Next Admin, now specifically designed for pharmacy product management with the following new features:

### New Features
1. **Product Management** (replaces tenant/property management)
   - Title, Category, Price with hide/show option
   - Unit dropdown (Tablet, Capsule, Syrup, ml, mg, etc.)
   - Dosage with dropdown options
   - Composition (active ingredients)
   - Company name
   - Search keywords
   - SKU tracking
   - Stock management

2. **Automatic Image Conversion to AVIF**
   - Uploads any image format (JPG, PNG, WEBP, GIF)
   - Automatically converts to AVIF for better compression
   - Reduces file size by 30-50% compared to JPEG
   - Falls back to WebP if AVIF not supported

3. **Category Management**
   - Create unlimited categories
   - Filter products by category
   - Category-wise statistics

4. **Advanced Search**
   - Search by title, keywords, composition, company
   - Full-text search capability
   - Category filtering

5. **Stock Management**
   - Track stock quantity
   - Low stock alerts
   - Out of stock indicators

## Quick Setup (5 Minutes)

### Step 1: Database Setup (2 minutes)
```bash
# Login to MySQL
mysql -u root -p

# Run the schema file
source pharma_database_schema.sql
```

OR use phpMyAdmin:
1. Create database `db_pharma_admin`
2. Import `pharma_database_schema.sql`

### Step 2: Configure (1 minute)
Edit `pharma_config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_pharma_admin');
define('DB_USER', 'root');        // Your MySQL user
define('DB_PASS', '');            // Your MySQL password
```

### Step 3: Set Permissions (1 minute)
```bash
chmod 755 uploads/products/
chmod 755 logs/
```

### Step 4: Access (1 minute)
1. Navigate to: `http://localhost/pharma-admin/login.php`
2. Login with:
   - Username: `admin`
   - Password: `Admin@123`
3. **Change password immediately!**

## File Checklist

Make sure these files are present:
- ✅ `pharma_config.php` - Configuration
- ✅ `pharma_database_schema.sql` - Database schema
- ✅ `pharma_dashboard.php` - Dashboard
- ✅ `products.php` - Product management
- ✅ `categories.php` - Category management
- ✅ `ImageConverter.php` - Image conversion utility
- ✅ `login.php`, `logout.php` - Authentication
- ✅ `db.php` - Database connection
- ✅ `SecureSession.php` - Session management
- ✅ `InputValidator.php` - Validation
- ✅ `include/header.php`, `include/footer.php` - Templates

## Main Pages

1. **Dashboard** (`pharma_dashboard.php`)
   - Overview of all statistics
   - Low stock alerts
   - Recent products
   - Category distribution

2. **Products** (`products.php`)
   - Add new products
   - List all products with filters
   - Search functionality
   - Image upload with auto-conversion

3. **Categories** (`categories.php`)
   - Manage product categories
   - View product counts per category

## Default Categories

The system comes with these pre-configured categories:
- Pain Relief
- Antibiotics
- Vitamins & Supplements
- Cardiac Care
- Diabetes Care
- Cold & Flu
- Digestive Health
- Skin Care
- Respiratory
- General Medicine

## Sample Products

4 sample products are pre-loaded:
1. Paracetamol 500mg (Pain Relief)
2. Amoxicillin 250mg (Antibiotics)
3. Vitamin D3 1000IU (Vitamins)
4. Cough Syrup (Cold & Flu)

## Common Issues & Solutions

### 1. Images not converting to AVIF
**Cause**: PHP version < 8.1
**Solution**: System will automatically use WebP format instead

### 2. Database connection failed
**Cause**: Wrong credentials
**Solution**: Check `pharma_config.php` database settings

### 3. Can't upload images
**Cause**: Permission issue
**Solution**: 
```bash
chmod 755 uploads/products/
chown www-data:www-data uploads/ -R
```

### 4. Login page not loading
**Cause**: Missing files
**Solution**: Ensure all files are extracted properly

## Testing the System

1. **Login**: Use admin/Admin@123
2. **Add Category**: Go to Categories → Add "Test Category"
3. **Add Product**: 
   - Go to Products
   - Fill form with sample data
   - Upload any image (will auto-convert to AVIF)
   - Save
4. **Search**: Try searching for the product
5. **Filter**: Filter by category

## Customization Tips

### Add More Units
Edit `pharma_config.php`:
```php
define('PRODUCT_UNITS', [
    'Tablet', 'Capsule', 'Syrup',
    'Your_New_Unit'  // Add here
]);
```

### Add More Dosage Options
Edit `pharma_config.php`:
```php
define('DOSAGE_OPTIONS', [
    '1 tablet daily',
    '2 tablets daily',
    'Your custom dosage'  // Add here
]);
```

### Change Products Per Page
Edit `pharma_config.php`:
```php
define('ITEMS_PER_PAGE', 50);  // Default is 20
```

## Security Checklist

- [ ] Changed default admin password
- [ ] Updated database credentials
- [ ] Set proper file permissions
- [ ] Enabled HTTPS (production)
- [ ] Created backup of database
- [ ] Reviewed access logs

## Next Steps

1. Change admin password
2. Add your product categories
3. Start adding products
4. Upload product images (auto-converts to AVIF)
5. Configure units and dosages as needed
6. Set up regular database backups

## Support

For detailed documentation, see `PHARMA_README.md`

---

**Important**: This is a complete replacement for the Urban Next admin. The tenant/property management has been replaced with pharmacy product management. All urban/property-related code has been removed and replaced with pharmaceutical product features.

## Key Differences from Urban Next Admin

| Urban Next | Pharma Admin |
|------------|--------------|
| Properties/Tenants | Products |
| Rent/Sale/Lease | Categories |
| Rooms, Bedrooms | Unit, Dosage |
| Contact Info | Company, Composition |
| Address | Keywords, SKU |
| Property Images | Product Images (AVIF) |
| No category system | Category management |
| No search keywords | Advanced search with keywords |
| No stock tracking | Stock management |
| Basic image upload | Auto AVIF conversion |

Enjoy your new Pharma Admin system! 🏥💊
