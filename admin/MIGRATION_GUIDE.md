# Migration Guide: Urban Next Admin → Pharma Admin

## Overview

This guide helps you transition from the Urban Next property management system to the new Pharma Admin pharmacy product management system.

## What Changed?

### Database Changes

| Old (Urban Next) | New (Pharma Admin) |
|------------------|-------------------|
| `db_urban_nest` | `db_pharma_admin` |
| `tenant` table | `products` table |
| `gallery` table | `product_images` table |
| No categories | `categories` table |

### Field Mapping

#### From Tenant → Product

| Old Field | New Field | Notes |
|-----------|-----------|-------|
| Property name | `title` | Product title |
| Cost | `price` | Product price |
| Address | `description` | Now used for description |
| Total rooms | - | Removed (not applicable) |
| Bedroom/Kitchen/Hall | - | Removed (not applicable) |
| Contact Name | `company_name` | Now manufacturer |
| Contact Number | - | Removed (not in product) |
| Contact Email | - | Removed (not in product) |
| City | `category_id` | Now uses categories |
| Tenant Type (rent/sale) | `category_id` | Categorized differently |
| Image | `product_images` | Separate table, AVIF format |

#### New Fields in Products

These fields are NEW and don't exist in Urban Next:

1. **`category_id`** - Links to categories table (required)
2. **`is_price_visible`** - Hide/show price option
3. **`unit`** - Measurement unit (Tablet, Capsule, ml, etc.)
4. **`dosage`** - Dosage instructions
5. **`composition`** - Active ingredients
6. **`company_name`** - Manufacturer name
7. **`keywords`** - Search keywords (comma-separated)
8. **`sku`** - Stock Keeping Unit (unique identifier)
9. **`stock_quantity`** - Current inventory
10. **`prescription_required`** - Boolean flag for Rx products
11. **`is_featured`** - Featured product flag

## Migration Options

### Option 1: Fresh Installation (Recommended)

**Best for**: Starting fresh with pharmacy products

1. Backup your old Urban Next database
2. Install Pharma Admin as new system
3. Manually add your products using the new interface

**Advantages**:
- Clean start
- No data conflicts
- Better data structure

### Option 2: Parallel Installation

**Best for**: Testing before full migration

1. Keep Urban Next admin running
2. Install Pharma Admin in separate directory
3. Use different database name
4. Test thoroughly before switching

### Option 3: Data Migration Script

If you have existing data you want to migrate, you can create a migration script. Here's a template:

```sql
-- MIGRATION SCRIPT (Customize as needed)
-- Backup first: mysqldump db_urban_nest > backup.sql

-- 1. Create new database
CREATE DATABASE db_pharma_admin;
USE db_pharma_admin;

-- 2. Run pharma_database_schema.sql first
-- source pharma_database_schema.sql;

-- 3. Create default category for migrated items
INSERT INTO categories (name, description, slug, is_active)
VALUES ('Migrated Products', 'Products migrated from old system', 'migrated', 1);

SET @migrated_category_id = LAST_INSERT_ID();

-- 4. Migrate products from tenant table
INSERT INTO products (
    title,
    category_id,
    price,
    description,
    company_name,
    keywords,
    is_active,
    created_at
)
SELECT 
    CONCAT('Property - ', address) as title,  -- Use address as title
    @migrated_category_id,
    cost as price,
    CONCAT('Rooms: ', total_rooms, ', Bedrooms: ', bedroom, 
           ', Kitchen: ', kitchen, ', Hall: ', hall) as description,
    contact_name as company_name,
    city as keywords,
    1 as is_active,
    created_at
FROM db_urban_nest.tenant;

-- 5. Note: Images need manual handling
-- Old images are in different format/location
-- You'll need to re-upload or convert manually
```

## Step-by-Step Migration Process

### Phase 1: Preparation (Day 1)

1. **Backup Everything**
   ```bash
   mysqldump -u root -p db_urban_nest > urban_nest_backup.sql
   tar -czf urban_nest_files.tar.gz /path/to/urban-next/
   ```

2. **Review Your Data**
   - How many properties/tenants do you have?
   - Do you need to keep all data?
   - What categories will you create?

3. **Plan Categories**
   - List product categories you'll need
   - Decide which old data maps to which category

### Phase 2: Installation (Day 1-2)

1. **Install Pharma Admin**
   - Follow `INSTALLATION_GUIDE.md`
   - Set up in parallel directory first

2. **Create Categories**
   - Go to Categories page
   - Add all your product categories

3. **Test with Sample Data**
   - Add 2-3 test products
   - Test image upload and AVIF conversion
   - Test search and filtering

### Phase 3: Data Migration (Day 2-3)

Choose one method:

**Method A: Manual Entry**
- Best for small datasets (< 50 items)
- Use the web interface
- Most accurate and clean

**Method B: Bulk Import**
- Best for large datasets
- Use SQL script (see template above)
- Requires customization
- Manual image re-upload needed

**Method C: CSV Import** (Create custom script)
- Export old data to CSV
- Transform data
- Import to new system

### Phase 4: Verification (Day 3-4)

1. **Check Data Integrity**
   - Verify all products migrated
   - Check prices are correct
   - Verify categories assigned properly

2. **Test Functionality**
   - Search for products
   - Filter by category
   - Check stock levels
   - Test price visibility

3. **Upload Images**
   - Re-upload product images
   - System will auto-convert to AVIF
   - Verify images display correctly

### Phase 5: Go Live (Day 5)

1. **Final Backup**
   ```bash
   mysqldump -u root -p db_pharma_admin > pharma_final_backup.sql
   ```

2. **Update Links**
   - Update any bookmarks
   - Redirect old URLs if needed

3. **Train Users**
   - Show new interface
   - Explain new features
   - Provide documentation

## Data Transformation Examples

### Example 1: Property → Product

**Old Data (Urban Next)**:
- Property: "3BHK Apartment in Downtown"
- Cost: ₹25,000
- City: Mumbai
- Contact: John Realty

**New Data (Pharma Admin)**:
- Title: "Paracetamol 500mg"
- Category: Pain Relief
- Price: ₹25.50
- Unit: Tablet
- Company: PharmaCorp
- Keywords: "pain, fever, headache"

### Example 2: Gallery Image → Product Image

**Old**: 
- Files in `/uploads/gallery/`
- Various formats (JPG, PNG)

**New**:
- Files in `/uploads/products/`
- Auto-converted to AVIF
- Smaller file sizes
- Better compression

## Post-Migration Checklist

- [ ] All products migrated/added
- [ ] Categories created and assigned
- [ ] Images uploaded and converted
- [ ] Prices verified
- [ ] Stock quantities set
- [ ] Units assigned
- [ ] Dosages entered (if applicable)
- [ ] Companies/manufacturers set
- [ ] Keywords added for search
- [ ] Featured products marked
- [ ] Prescription flags set
- [ ] Old system backed up
- [ ] New system tested thoroughly
- [ ] Users trained on new interface
- [ ] Documentation distributed

## Rollback Plan

If you need to go back to Urban Next:

1. **Keep backups** of both systems
2. **Don't delete** old database immediately
3. **Maintain parallel systems** for 1-2 weeks
4. **Test thoroughly** before decommissioning old system

```bash
# Restore Urban Next if needed
mysql -u root -p db_urban_nest < urban_nest_backup.sql
```

## Common Questions

**Q: Can I import my old property images?**
A: Yes, but they need to be re-uploaded through the new interface for AVIF conversion.

**Q: Will search work on old data?**
A: Only if you add keywords to the products. The old system didn't have keyword support.

**Q: Can I keep both systems running?**
A: Yes, they use different databases and can coexist.

**Q: Do I need to re-enter all data?**
A: Depends on data volume. For <50 items, manual entry is faster and cleaner.

**Q: What about user accounts?**
A: User table structure is the same, you can migrate users directly.

## Support

For migration assistance:
1. Review `PHARMA_README.md` for full documentation
2. Check `INSTALLATION_GUIDE.md` for setup help
3. Test in development environment first
4. Keep backups at every step

## Timeline Estimate

- **Small Dataset** (<50 items): 1-2 days
- **Medium Dataset** (50-200 items): 3-5 days
- **Large Dataset** (>200 items): 1-2 weeks

Remember: Take your time, test thoroughly, and keep backups!

---

Good luck with your migration! 🏥💊
