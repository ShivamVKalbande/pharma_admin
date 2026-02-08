<?php
require_once 'pharma_config.php';
require_once 'db.php';
require_once 'SecureSession.php';
require_once 'InputValidator.php';
require_once 'ImageConverter.php';

SecureSession::init();
SecureSession::requireLogin();

$db = getDB();
$errors = [];
$success = '';

// Get all categories for dropdown
$categoriesQuery = "SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order, name";
$categoriesStmt = $db->prepare($categoriesQuery);
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !SecureSession::validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = "Invalid security token";
    } else {
        
        if ($_POST['action'] === 'add_product') {
            // Get and sanitize input
            $title = InputValidator::sanitizeString($_POST['title'] ?? '');
            $category_id = InputValidator::sanitizeInt($_POST['category_id'] ?? '');
            $price = InputValidator::sanitizeFloat($_POST['price'] ?? '');
            $is_price_visible = isset($_POST['is_price_visible']) ? 1 : 0;
            $unit = InputValidator::sanitizeString($_POST['unit'] ?? '');
            $description = InputValidator::sanitizeString($_POST['description'] ?? '');
            $dosage = InputValidator::sanitizeString($_POST['dosage'] ?? '');
            $composition = InputValidator::sanitizeString($_POST['composition'] ?? '');
            $company_name = InputValidator::sanitizeString($_POST['company_name'] ?? '');
            $keywords = InputValidator::sanitizeString($_POST['keywords'] ?? '');
            $sku = InputValidator::sanitizeString($_POST['sku'] ?? '');
            $stock_quantity = InputValidator::sanitizeInt($_POST['stock_quantity'] ?? '0');
            $prescription_required = isset($_POST['prescription_required']) ? 1 : 0;
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            
            // Validate required fields
            if (empty($title)) {
                $errors[] = "Product title is required";
            }
            if (empty($category_id) || $category_id <= 0) {
                $errors[] = "Please select a category";
            }
            if (empty($price) || $price < 0) {
                $errors[] = "Valid price is required";
            }
            if (empty($unit)) {
                $errors[] = "Unit is required";
            }
            
            // Validate lengths
            if (strlen($title) > 255) {
                $errors[] = "Title must be less than 255 characters";
            }
            if (strlen($sku) > 50) {
                $errors[] = "SKU must be less than 50 characters";
            }
            
            // Handle image upload and conversion
            $imageName = null;
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = ImageConverter::convertToAVIF($_FILES['product_image']);
                    if ($uploadResult['success']) {
                        $imageName = $uploadResult['filename'];
                        $originalImageName = $uploadResult['original_name'];
                    } else {
                        $errors = array_merge($errors, $uploadResult['errors']);
                    }
                } else {
                    $errors[] = "Error uploading image";
                }
            }
            
            // If no errors, insert into database
            if (empty($errors)) {
                try {
                    $db->beginTransaction();
                    
                    // Insert product
                    $query = "INSERT INTO products (
                        title, category_id, price, is_price_visible, unit, description,
                        dosage, composition, company_name, keywords, sku, stock_quantity,
                        prescription_required, is_featured, created_by
                    ) VALUES (
                        :title, :category_id, :price, :is_price_visible, :unit, :description,
                        :dosage, :composition, :company_name, :keywords, :sku, :stock_quantity,
                        :prescription_required, :is_featured, :created_by
                    )";
                    
                    $stmt = $db->prepare($query);
                    $stmt->execute([
                        ':title' => $title,
                        ':category_id' => $category_id,
                        ':price' => $price,
                        ':is_price_visible' => $is_price_visible,
                        ':unit' => $unit,
                        ':description' => $description,
                        ':dosage' => $dosage,
                        ':composition' => $composition,
                        ':company_name' => $company_name,
                        ':keywords' => $keywords,
                        ':sku' => $sku,
                        ':stock_quantity' => $stock_quantity,
                        ':prescription_required' => $prescription_required,
                        ':is_featured' => $is_featured,
                        ':created_by' => $_SESSION['user_id'] ?? null
                    ]);
                    
                    $productId = $db->lastInsertId();
                    
                    // Insert image if uploaded
                    if ($imageName) {
                        $imageQuery = "INSERT INTO product_images (product_id, image_name, original_name, is_primary) 
                                      VALUES (:product_id, :image_name, :original_name, 1)";
                        $imageStmt = $db->prepare($imageQuery);
                        $imageStmt->execute([
                            ':product_id' => $productId,
                            ':image_name' => $imageName,
                            ':original_name' => $originalImageName
                        ]);
                    }
                    
                    $db->commit();
                    $success = MSG_SUCCESS_ADD;
                    
                    // Clear form
                    $_POST = [];
                    
                } catch (PDOException $e) {
                    $db->rollBack();
                    if ($imageName) {
                        ImageConverter::deleteImage($imageName);
                    }
                    error_log("Database error: " . $e->getMessage());
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $errors[] = "SKU already exists. Please use a unique SKU.";
                    } else {
                        $errors[] = "Database error: " . $e->getMessage();
                    }
                }
            }
        }
        
        // Handle delete action
        elseif ($_POST['action'] === 'delete_product') {
            $product_id = InputValidator::sanitizeInt($_POST['product_id'] ?? '');
            
            if ($product_id > 0) {
                try {
                    // Get product images first
                    $imageQuery = "SELECT image_name FROM product_images WHERE product_id = :product_id";
                    $imageStmt = $db->prepare($imageQuery);
                    $imageStmt->execute([':product_id' => $product_id]);
                    $images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Delete product (cascade will delete images from DB)
                    $deleteQuery = "DELETE FROM products WHERE id = :id";
                    $deleteStmt = $db->prepare($deleteQuery);
                    $deleteStmt->execute([':id' => $product_id]);
                    
                    // Delete physical image files
                    foreach ($images as $image) {
                        ImageConverter::deleteImage($image['image_name']);
                    }
                    
                    $success = MSG_SUCCESS_DELETE;
                    
                } catch (PDOException $e) {
                    error_log("Delete error: " . $e->getMessage());
                    $errors[] = "Failed to delete product";
                }
            }
        }
    }
}

// Get filter parameters
$filter_category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * ITEMS_PER_PAGE;

// Build query with filters
$whereConditions = [];
$params = [];

if (!empty($filter_category)) {
    $whereConditions[] = "p.category_id = :category_id";
    $params[':category_id'] = $filter_category;
}

if (!empty($search)) {
    $whereConditions[] = "(p.title LIKE :search OR p.keywords LIKE :search OR p.composition LIKE :search OR p.company_name LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM products p $whereClause";
$countStmt = $db->prepare($countQuery);
$countStmt->execute($params);
$totalProducts = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalProducts / ITEMS_PER_PAGE);

// Get products with pagination
$query = "SELECT p.*, c.name as category_name, 
          (SELECT image_name FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          $whereClause
          ORDER BY p.created_at DESC
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', ITEMS_PER_PAGE, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Product Management";
require_once 'include/header.php';
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Product Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">Error!</h4>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add Product Card -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Add New Product</h5>

                <form method="POST" enctype="multipart/form-data" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?php echo SecureSession::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="add_product">

                    <div class="col-md-6">
                        <label for="title" class="form-label">Product Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="price" class="form-label">Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required
                               value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select" id="unit" name="unit" required>
                            <option value="">Select Unit</option>
                            <?php foreach (PRODUCT_UNITS as $unit_option): ?>
                                <option value="<?php echo $unit_option; ?>"
                                    <?php echo (isset($_POST['unit']) && $_POST['unit'] == $unit_option) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($unit_option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="dosage" class="form-label">Dosage</label>
                        <select class="form-select" id="dosage" name="dosage">
                            <option value="">Select Dosage</option>
                            <?php foreach (DOSAGE_OPTIONS as $dosage_option): ?>
                                <option value="<?php echo $dosage_option; ?>"
                                    <?php echo (isset($_POST['dosage']) && $_POST['dosage'] == $dosage_option) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dosage_option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Or type custom dosage</small>
                    </div>

                    <div class="col-md-6">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name"
                               value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" class="form-control" id="sku" name="sku"
                               value="<?php echo htmlspecialchars($_POST['sku'] ?? ''); ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="stock_quantity" class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0"
                               value="<?php echo htmlspecialchars($_POST['stock_quantity'] ?? '0'); ?>">
                    </div>

                    <div class="col-md-12">
                        <label for="composition" class="form-label">Composition</label>
                        <textarea class="form-control" id="composition" name="composition" rows="2"><?php echo htmlspecialchars($_POST['composition'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <label for="keywords" class="form-label">Search Keywords</label>
                        <input type="text" class="form-control" id="keywords" name="keywords" 
                               placeholder="e.g., pain relief, headache, fever (comma-separated)"
                               value="<?php echo htmlspecialchars($_POST['keywords'] ?? ''); ?>">
                        <small class="text-muted">Keywords help users find this product in search</small>
                    </div>

                    <div class="col-md-12">
                        <label for="product_image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
                        <small class="text-muted">Supported formats: JPG, PNG, WEBP, GIF. Will be converted to AVIF for better compression.</small>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_price_visible" name="is_price_visible" 
                                   <?php echo (isset($_POST['is_price_visible']) || !isset($_POST['action'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_price_visible">
                                Show Price (Uncheck to hide price from users)
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="prescription_required" name="prescription_required"
                                   <?php echo (isset($_POST['prescription_required'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="prescription_required">
                                Prescription Required
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                   <?php echo (isset($_POST['is_featured'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_featured">
                                Featured Product
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Product
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Products List Card -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Products List</h5>

                <!-- Filters -->
                <form method="GET" class="row g-3 mb-3">
                    <div class="col-md-4">
                        <select class="form-select" name="category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo ($filter_category == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="search" placeholder="Search products..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Unit</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No products found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <?php if ($product['primary_image']): ?>
                                                <img src="<?php echo ImageConverter::getImagePath($product['primary_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['title']); ?>"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div style="width: 50px; height: 50px; background: #ddd; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product['title']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($product['company_name']); ?></small>
                                            <?php if ($product['prescription_required']): ?>
                                                <br><span class="badge bg-warning">Rx Required</span>
                                            <?php endif; ?>
                                            <?php if ($product['is_featured']): ?>
                                                <span class="badge bg-success">Featured</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                        <td>
                                            <?php if ($product['is_price_visible']): ?>
                                                ₹<?php echo number_format($product['price'], 2); ?>
                                            <?php else: ?>
                                                <span class="text-muted"><i class="bi bi-eye-slash"></i> Hidden</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['unit']); ?></td>
                                        <td>
                                            <?php if ($product['stock_quantity'] > 10): ?>
                                                <span class="badge bg-success"><?php echo $product['stock_quantity']; ?></span>
                                            <?php elseif ($product['stock_quantity'] > 0): ?>
                                                <span class="badge bg-warning"><?php echo $product['stock_quantity']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Out of Stock</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($product['is_active']): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewProduct(<?php echo $product['id']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <form method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                <input type="hidden" name="csrf_token" 
                                                       value="<?php echo SecureSession::generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete_product">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($filter_category) ? '&category=' . $filter_category : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                    Previous
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($filter_category) ? '&category=' . $filter_category : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($filter_category) ? '&category=' . $filter_category : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </div>

    </section>
</main>

<script>
function viewProduct(id) {
    // You can implement a modal or redirect to view page
    alert('View product ID: ' + id + '\nThis would show detailed product information.');
}
</script>

<?php
require_once 'include/footer.php';
?>
