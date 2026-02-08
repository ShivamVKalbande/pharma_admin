<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/product_list_errors.log');

try {
    require_once 'pharma_config.php';
} catch (Exception $e) {
    die("Config Error: " . $e->getMessage());
}

try {
    require_once 'db.php';
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

try {
    require_once 'SecureSession.php';
} catch (Exception $e) {
    die("Session Error: " . $e->getMessage());
}

try {
    require_once 'InputValidator.php';
} catch (Exception $e) {
    die("Validator Error: " . $e->getMessage());
}

try {
    require_once 'ImageConverter.php';
} catch (Exception $e) {
    die("ImageConverter Error: " . $e->getMessage());
}

SecureSession::init();
SecureSession::requireLogin();

try {
    $db = getDB();
} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage() . "<br>Please check your database configuration in pharma_config.php");
}

$errors = [];
$success = '';

// Get all categories for dropdown
try {
    $categoriesQuery = "SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order, name";
    $categoriesStmt = $db->prepare($categoriesQuery);
    $categoriesStmt->execute();
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Error loading categories: " . $e->getMessage();
    $categories = [];
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_product') {
    
    if (!isset($_POST['csrf_token']) || !SecureSession::validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = "Invalid security token";
    } else {
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
                
                $success = "Product deleted successfully!";
                
            } catch (PDOException $e) {
                error_log("Delete error: " . $e->getMessage());
                $errors[] = "Failed to delete product";
            }
        }
    }
}

// Get filter parameters (removed prescription and stock filters)
$filter_category = $_GET['category'] ?? '';
$filter_unit = $_GET['unit'] ?? '';
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

if (!empty($filter_unit)) {
    $whereConditions[] = "p.unit = :unit";
    $params[':unit'] = $filter_unit;
}

if (!empty($search)) {
    $whereConditions[] = "(p.title LIKE :search1 OR p.keywords LIKE :search2 OR p.composition LIKE :search3 OR p.company_name LIKE :search4 OR p.sku LIKE :search5)";
    $searchParam = '%' . $search . '%';
    $params[':search1'] = $searchParam;
    $params[':search2'] = $searchParam;
    $params[':search3'] = $searchParam;
    $params[':search4'] = $searchParam;
    $params[':search5'] = $searchParam;
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
// Bind search/filter parameters first
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
// Then bind pagination parameters
$stmt->bindValue(':limit', ITEMS_PER_PAGE, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
// Execute WITHOUT passing params again (they're already bound)
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique units for filter
try {
    $unitsQuery = "SELECT DISTINCT unit FROM products WHERE is_active = 1 ORDER BY unit";
    $unitsStmt = $db->prepare($unitsQuery);
    $unitsStmt->execute();
    $units = $unitsStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $errors[] = "Error loading units: " . $e->getMessage();
    $units = [];
}

$pageTitle = "Product List";
require_once 'include/header.php';
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Product List</h1>
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
                <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="add_product.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle"></i> Add New Product
                </a>
                <a href="categories.php" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-grid"></i> Manage Categories
                </a>
            </div>
        </div>

        <!-- Filters and Search Card -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-funnel"></i> Search & Filter Products
                    <?php if (!empty($search) || !empty($filter_category) || !empty($filter_unit)): ?>
                        <a href="product_list.php" class="btn btn-sm btn-outline-danger float-end">
                            <i class="bi bi-x-circle"></i> Clear All Filters
                        </a>
                    <?php endif; ?>
                </h5>

                <form method="GET" id="filter-form" class="row g-3">
                    
                    <!-- Live Search with Suggestions -->
                    <div class="col-md-12">
                        <label for="search" class="form-label"><i class="bi bi-search"></i> Search Products</label>
                        <div class="position-relative">
                            <input type="text" class="form-control form-control-lg" id="search" name="search" 
                                   placeholder="Search by name, keywords, composition, company, or SKU..."
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   autocomplete="off">
                            <div id="search-suggestions" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 300px; overflow-y: auto;"></div>
                        </div>
                        <small class="text-muted">Start typing to see suggestions</small>
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="form-label"><i class="bi bi-grid"></i> Category</label>
                        <select class="form-select" name="category" id="category" onchange="this.form.submit()">
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
                        <label for="unit" class="form-label"><i class="bi bi-box"></i> Unit Type</label>
                        <select class="form-select" name="unit" id="unit" onchange="this.form.submit()">
                            <option value="">All Units</option>
                            <?php foreach ($units as $unit_opt): ?>
                                <option value="<?php echo $unit_opt; ?>" 
                                    <?php echo ($filter_unit == $unit_opt) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($unit_opt); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Apply Filters
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('search').value=''; this.form.submit();">
                            <i class="bi bi-arrow-clockwise"></i> Reset Search
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Products List Card -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    Products 
                    <span class="badge bg-primary"><?php echo $totalProducts; ?> total</span>
                </h5>

                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="products-table">
                        <thead class="table-dark">
                            <tr>
                                <th width="80">Image</th>
                                <th>Product Details</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">No products found matching your criteria</p>
                                        <a href="add_product.php" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Add First Product
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <?php if ($product['primary_image']): ?>
                                                <img src="<?php echo ImageConverter::getImagePath($product['primary_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['title']); ?>"
                                                     class="img-thumbnail"
                                                     style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                     onclick="showImageModal('<?php echo ImageConverter::getImagePath($product['primary_image']); ?>', '<?php echo htmlspecialchars($product['title']); ?>')">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong class="d-block"><?php echo htmlspecialchars($product['title']); ?></strong>
                                            <?php if ($product['company_name']): ?>
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-building"></i> <?php echo htmlspecialchars($product['company_name']); ?>
                                                </small>
                                            <?php endif; ?>
                                            <?php if ($product['sku']): ?>
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-upc"></i> SKU: <?php echo htmlspecialchars($product['sku']); ?>
                                                </small>
                                            <?php endif; ?>
                                            <div class="mt-1">
                                                <?php if ($product['is_featured']): ?>
                                                    <span class="badge bg-success"><i class="bi bi-star-fill"></i> Featured</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                        </td>
                                        <td>
                                            <?php if ($product['is_price_visible']): ?>
                                                <strong class="text-success">$<?php echo number_format($product['price'], 2); ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    <i class="bi bi-eye-slash"></i> Hidden
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['unit']); ?></td>
                                        <td>
                                            <?php if ($product['is_active']): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-info" onclick="viewProduct(<?php echo $product['id']; ?>)" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary" title="Edit Product">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete this product?\n\n<?php echo htmlspecialchars($product['title']); ?>');">
                                                    <input type="hidden" name="csrf_token" 
                                                           value="<?php echo SecureSession::generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="delete_product">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($filter_category) ? '&category=' . $filter_category : ''; ?><?php echo !empty($filter_unit) ? '&unit=' . urlencode($filter_unit) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                    <i class="bi bi-chevron-left"></i> Previous
                                </a>
                            </li>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($totalPages, $page + 2);
                            
                            if ($start_page > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=1<?php echo !empty($filter_category) ? '&category=' . $filter_category : ''; ?><?php echo !empty($filter_unit) ? '&unit=' . urlencode($filter_unit) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">1</a></li>
                                <?php if ($start_page > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($filter_category) ? '&category=' . $filter_category : ''; ?><?php echo !empty($filter_unit) ? '&unit=' . urlencode($filter_unit) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($end_page < $totalPages): ?>
                                <?php if ($end_page < $totalPages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $totalPages; ?><?php echo !empty($filter_category) ? '&category=' . $filter_category : ''; ?><?php echo !empty($filter_unit) ? '&unit=' . urlencode($filter_unit) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo $totalPages; ?></a></li>
                            <?php endif; ?>
                            
                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($filter_category) ? '&category=' . $filter_category : ''; ?><?php echo !empty($filter_unit) ? '&unit=' . urlencode($filter_unit) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                    Next <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                        <p class="text-center text-muted">
                            Showing <?php echo min(($page - 1) * ITEMS_PER_PAGE + 1, $totalProducts); ?> 
                            to <?php echo min($page * ITEMS_PER_PAGE, $totalProducts); ?> 
                            of <?php echo $totalProducts; ?> products
                        </p>
                    </nav>
                <?php endif; ?>

            </div>
        </div>

    </section>
</main>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Product Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
// Live Search with Suggestions
let searchTimeout;
const searchInput = document.getElementById('search');
const suggestionsBox = document.getElementById('search-suggestions');

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
        suggestionsBox.style.display = 'none';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetchSuggestions(query);
    }, 300);
});

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
        suggestionsBox.style.display = 'none';
    }
});

function fetchSuggestions(query) {
    fetch('search_suggestions.php?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '';
                data.forEach(item => {
                    html += `
                        <a href="#" class="list-group-item list-group-item-action" onclick="selectSuggestion('${escapeHtml(item.title)}'); return false;">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <strong>${highlightMatch(item.title, query)}</strong>
                                    ${item.company_name ? '<br><small class="text-muted">' + item.company_name + '</small>' : ''}
                                </div>
                                <div class="text-end">
                                    <small class="badge bg-info">${item.category_name}</small>
                                    ${item.sku ? '<br><small class="text-muted">SKU: ' + item.sku + '</small>' : ''}
                                </div>
                            </div>
                        </a>
                    `;
                });
                suggestionsBox.innerHTML = html;
                suggestionsBox.style.display = 'block';
            } else {
                suggestionsBox.innerHTML = '<div class="list-group-item text-muted">No suggestions found</div>';
                suggestionsBox.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error fetching suggestions:', error);
            suggestionsBox.style.display = 'none';
        });
}

function selectSuggestion(value) {
    searchInput.value = value;
    suggestionsBox.style.display = 'none';
    document.getElementById('filter-form').submit();
}

function highlightMatch(text, query) {
    const regex = new RegExp('(' + query + ')', 'gi');
    return text.replace(regex, '<mark>$1</mark>');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// View Product Details
function viewProduct(id) {
    // Fetch product details via AJAX
    fetch('get_product_details.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showProductModal(data.product);
            } else {
                alert('Failed to load product details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading product details');
        });
}

function showProductModal(product) {
    const modalHtml = `
        <div class="modal fade" id="productModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-box-seam"></i> ${product.title}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <!-- Left Column - Image -->
                            <div class="col-md-5">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-0">
                                        ${product.primary_image ? 
                                            '<img src="' + product.primary_image + '" class="img-fluid w-100 rounded" alt="' + product.title + '" style="max-height: 400px; object-fit: contain; background: #f8f9fa; padding: 20px;">' : 
                                            '<div class="bg-light p-5 text-center rounded" style="min-height: 400px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-image text-muted" style="font-size: 6rem;"></i></div>'}
                                    </div>
                                </div>
                                
                                <!-- Badges -->
                                <div class="mt-3 d-flex gap-2 flex-wrap">
                                    ${product.is_featured ? '<span class="badge bg-success fs-6"><i class="bi bi-star-fill"></i> Featured</span>' : ''}
                                    ${product.is_active ? '<span class="badge bg-primary fs-6"><i class="bi bi-check-circle"></i> Active</span>' : '<span class="badge bg-secondary fs-6">Inactive</span>'}
                                </div>
                            </div>
                            
                            <!-- Right Column - Details -->
                            <div class="col-md-7">
                                <!-- Product Info -->
                                <div class="mb-4">
                                    <h3 class="mb-3 text-primary">${product.title}</h3>
                                    ${product.company_name ? '<p class="text-muted mb-2"><i class="bi bi-building"></i> <strong>Manufacturer:</strong> ' + product.company_name + '</p>' : ''}
                                    ${product.sku ? '<p class="text-muted mb-2"><i class="bi bi-upc"></i> <strong>SKU:</strong> <code>' + product.sku + '</code></p>' : ''}
                                </div>
                                
                                <!-- Price Card -->
                                <div class="card bg-light border-0 mb-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted d-block">Price</small>
                                                ${product.is_price_visible ? 
                                                    '<h2 class="mb-0 text-success">$' + parseFloat(product.price).toFixed(2) + '</h2>' : 
                                                    '<h5 class="mb-0 text-muted"><i class="bi bi-eye-slash"></i> Price Hidden</h5>'}
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">Category</small>
                                                <h6 class="mb-0"><span class="badge bg-info fs-6">${product.category_name}</span></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Details Table -->
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Product Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th width="150" class="text-muted">Unit:</th>
                                                    <td><strong>${product.unit}</strong></td>
                                                </tr>
                                                ${product.composition ? '<tr><th class="text-muted align-top">Composition:</th><td>' + product.composition + '</td></tr>' : ''}
                                                ${product.description ? '<tr><th class="text-muted align-top">Description:</th><td>' + product.description + '</td></tr>' : ''}
                                                ${product.keywords ? '<tr><th class="text-muted align-top">Keywords:</th><td><small class="text-muted">' + product.keywords + '</small></td></tr>' : ''}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Timestamps -->
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-clock-history"></i> Created: ${new Date(product.created_at).toLocaleDateString()} | 
                                        Updated: ${new Date(product.updated_at).toLocaleDateString()}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="edit_product.php?id=${product.id}" class="btn btn-primary">
                            <i class="bi bi-pencil-square"></i> Edit Product
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('productModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add and show new modal
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
    
    // Cleanup on close
    document.getElementById('productModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Show Image Modal
function showImageModal(src, title) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModalLabel').textContent = title;
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}
</script>

<?php
require_once 'include/footer.php';
?>