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

// Handle add new category via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_category') {
    header('Content-Type: application/json');
    
    if (!isset($_POST['csrf_token']) || !SecureSession::validateCSRFToken($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        exit;
    }
    
    $category_name = InputValidator::sanitizeString($_POST['category_name'] ?? '');
    
    if (empty($category_name)) {
        echo json_encode(['success' => false, 'message' => 'Category name is required']);
        exit;
    }
    
    try {
        // Generate slug from category name
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $category_name)));
        
        $query = "INSERT INTO categories (name, slug, is_active) VALUES (:name, :slug, 1)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':name' => $category_name,
            ':slug' => $slug
        ]);
        
        $newCategoryId = $db->lastInsertId();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Category added successfully',
            'category_id' => $newCategoryId,
            'category_name' => $category_name
        ]);
        exit;
        
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            echo json_encode(['success' => false, 'message' => 'Category already exists']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add category']);
        }
        exit;
    }
}

// Get all categories for dropdown
$categoriesQuery = "SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order, name";
$categoriesStmt = $db->prepare($categoriesQuery);
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Function to generate SKU
function generateSKU($db) {
    // Get the last product ID
    $query = "SELECT IFNULL(MAX(id), 0) + 1 as next_id FROM products";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextId = $result['next_id'];
    
    // Generate SKU in format PROD-00001, PROD-00002, etc.
    return 'PROD-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !SecureSession::validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = "Invalid security token";
    } else {
        
        // Get and sanitize input (removed: sku, stock_quantity, dosage, prescription_required)
        $title = InputValidator::sanitizeString($_POST['title'] ?? '');
        $category_id = InputValidator::sanitizeInt($_POST['category_id'] ?? '');
        $price = InputValidator::sanitizeFloat($_POST['price'] ?? '');
        $is_price_visible = isset($_POST['is_price_visible']) ? 1 : 0;
        $unit = InputValidator::sanitizeString($_POST['unit'] ?? '');
        $description = InputValidator::sanitizeString($_POST['description'] ?? '');
        $composition = InputValidator::sanitizeString($_POST['composition'] ?? '');
        $company_name = InputValidator::sanitizeString($_POST['company_name'] ?? '');
        $keywords = InputValidator::sanitizeString($_POST['keywords'] ?? '');
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
        if (strlen($keywords) > 500) {
            $errors[] = "Keywords must be less than 500 characters";
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
                
                // Generate SKU automatically
                $sku = generateSKU($db);
                
                // Insert product (removed: dosage, stock_quantity, prescription_required)
                $query = "INSERT INTO products (
                    sku, title, category_id, price, is_price_visible, unit, description,
                    composition, company_name, keywords, is_featured, created_by
                ) VALUES (
                    :sku, :title, :category_id, :price, :is_price_visible, :unit, :description,
                    :composition, :company_name, :keywords, :is_featured, :created_by
                )";
                
                $stmt = $db->prepare($query);
                $stmt->execute([
                    ':sku' => $sku,
                    ':title' => $title,
                    ':category_id' => $category_id,
                    ':price' => $price,
                    ':is_price_visible' => $is_price_visible,
                    ':unit' => $unit,
                    ':description' => $description,
                    ':composition' => $composition,
                    ':company_name' => $company_name,
                    ':keywords' => $keywords,
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
                $success = "Product added successfully with SKU: <strong>$sku</strong>! <a href='product_list.php' class='alert-link'>View Products</a>";
                
                // Clear form
                $_POST = [];
                
            } catch (PDOException $e) {
                $db->rollBack();
                if ($imageName) {
                    ImageConverter::deleteImage($imageName);
                }
                error_log("Database error: " . $e->getMessage());
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $errors[] = "SKU already exists. Please try again.";
                } else {
                    $errors[] = "Database error: " . $e->getMessage();
                }
            }
        }
    }
}

$pageTitle = "Add Product";
require_once 'include/header.php';
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Add New Product</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item"><a href="product_list.php">Products</a></li>
                <li class="breadcrumb-item active">Add Product</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Error!</h4>
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
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add Product Card -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Product Information</h5>

                <form method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo SecureSession::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="add_product">

                    <!-- Basic Information -->
                    <div class="col-12">
                        <h6 class="text-primary"><i class="bi bi-info-circle"></i> Basic Information</h6>
                        <hr>
                    </div>

                    <div class="col-md-6">
                        <label for="title" class="form-label">Product Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required 
                               placeholder="e.g., Paracetamol 500mg"
                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        <div class="invalid-feedback">Please enter product title.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-outline-primary" type="button" id="addCategoryBtn" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="bi bi-plus-circle"></i> Add New
                            </button>
                        </div>
                        <div class="invalid-feedback">Please select a category.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="company_name" class="form-label">Manufacturer/Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name"
                               placeholder="e.g., PharmaCorp Ltd"
                               value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6">
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
                        <div class="invalid-feedback">Please select a unit.</div>
                    </div>

                    <!-- Pricing -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary"><i class="bi bi-currency-dollar"></i> Pricing</h6>
                        <hr>
                    </div>

                    <div class="col-md-6">
                        <label for="price" class="form-label">Price (USD $) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required
                                   placeholder="0.00"
                                   value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                        </div>
                        <div class="invalid-feedback">Please enter a valid price.</div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="is_price_visible" name="is_price_visible" 
                                   <?php echo (isset($_POST['is_price_visible']) || !isset($_POST['action'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_price_visible">
                                <i class="bi bi-eye"></i> Show Price to Users
                            </label>
                            <small class="d-block text-muted">Uncheck to hide price from customers</small>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary"><i class="bi bi-file-text"></i> Product Details</h6>
                        <hr>
                    </div>

                    <div class="col-md-12">
                        <label for="composition" class="form-label">Composition / Active Ingredients</label>
                        <textarea class="form-control" id="composition" name="composition" rows="2"
                                  placeholder="e.g., Paracetamol 500mg, Caffeine 65mg"><?php echo htmlspecialchars($_POST['composition'] ?? ''); ?></textarea>
                        <small class="text-muted">List the active ingredients and their quantities</small>
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                                  placeholder="Product description, benefits, usage instructions..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <label for="keywords" class="form-label">Search Keywords <i class="bi bi-question-circle" data-bs-toggle="tooltip" title="Keywords help users find this product"></i></label>
                        <input type="text" class="form-control" id="keywords" name="keywords" 
                               placeholder="e.g., pain relief, headache, fever, analgesic (comma-separated)"
                               maxlength="500"
                               value="<?php echo htmlspecialchars($_POST['keywords'] ?? ''); ?>">
                        <small class="text-muted">Add keywords to improve searchability (max 500 characters)</small>
                    </div>

                    <!-- Image Upload -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary"><i class="bi bi-image"></i> Product Image</h6>
                        <hr>
                    </div>

                    <div class="col-md-12">
                        <label for="product_image" class="form-label">Upload Image</label>
                        <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*" onchange="previewImage(this)">
                        <small class="text-muted">Supported: JPG, PNG, WEBP, GIF. Auto-converts to AVIF for compression.</small>
                        
                        <div id="image-preview" class="mt-3" style="display: none;">
                            <p class="mb-2"><strong>Preview:</strong></p>
                            <img id="preview-img" src="" alt="Preview" style="max-width: 300px; max-height: 300px; border: 1px solid #ddd; padding: 5px;">
                        </div>
                    </div>

                    <!-- Product Options -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary"><i class="bi bi-toggles"></i> Product Options</h6>
                        <hr>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                   <?php echo (isset($_POST['is_featured'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_featured">
                                <i class="bi bi-star"></i> Featured Product
                            </label>
                            <small class="d-block text-muted">Highlight this product on homepage</small>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 mt-4">
                        <hr>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle"></i> Add Product
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Clear Form
                        </button>
                        <a href="product_list.php" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-list"></i> View Products
                        </a>
                    </div>
                </form>

            </div>
        </div>

    </section>
</main>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm">
                    <input type="hidden" name="csrf_token" value="<?php echo SecureSession::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="add_category">
                    <div class="mb-3">
                        <label for="new_category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new_category_name" name="category_name" required>
                    </div>
                    <div id="category-alert" class="alert d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    <i class="bi bi-check-circle"></i> Save Category
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Enable Bootstrap validation
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// Image preview
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

// Add Category functionality
document.getElementById('saveCategoryBtn').addEventListener('click', function() {
    const form = document.getElementById('addCategoryForm');
    const formData = new FormData(form);
    const alertDiv = document.getElementById('category-alert');
    
    fetch('add_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new option to select dropdown
            const select = document.getElementById('category_id');
            const option = document.createElement('option');
            option.value = data.category_id;
            option.text = data.category_name;
            option.selected = true;
            select.add(option);
            
            // Show success message
            alertDiv.className = 'alert alert-success';
            alertDiv.textContent = data.message;
            alertDiv.classList.remove('d-none');
            
            // Close modal after 1 second
            setTimeout(function() {
                bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
                form.reset();
                alertDiv.classList.add('d-none');
            }, 1000);
        } else {
            // Show error message
            alertDiv.className = 'alert alert-danger';
            alertDiv.textContent = data.message;
            alertDiv.classList.remove('d-none');
        }
    })
    .catch(error => {
        alertDiv.className = 'alert alert-danger';
        alertDiv.textContent = 'An error occurred. Please try again.';
        alertDiv.classList.remove('d-none');
    });
});

// Reset alert when modal is closed
document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('addCategoryForm').reset();
    document.getElementById('category-alert').classList.add('d-none');
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>

<?php
require_once 'include/footer.php';
?>
