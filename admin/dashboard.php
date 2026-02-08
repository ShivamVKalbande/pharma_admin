<?php
require_once 'pharma_config.php';
require_once 'db.php';
require_once 'SecureSession.php';

SecureSession::init();
SecureSession::requireLogin();

$db = getDB();

// Get statistics
$stats = [];

// Total products
$query = "SELECT COUNT(*) as total FROM products WHERE is_active = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total categories
$query = "SELECT COUNT(*) as total FROM categories WHERE is_active = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Featured products
$query = "SELECT COUNT(*) as total FROM products WHERE is_featured = 1 AND is_active = 1";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['featured_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get recent products
$query = "SELECT p.*, c.name as category_name,
          (SELECT image_name FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.is_active = 1
          ORDER BY p.created_at DESC
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get category-wise product count
$query = "SELECT c.name, COUNT(p.id) as product_count
          FROM categories c
          LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
          WHERE c.is_active = 1
          GROUP BY c.id, c.name
          ORDER BY product_count DESC
          LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();
$category_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Dashboard";
require_once 'include/header.php';
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">

                    <!-- Total Products Card -->
                    <div class="col-xxl-4 col-md-6">
                        <a href="product_list.php">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Products</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-capsule"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?php echo $stats['total_products']; ?></h6>
                                            <span class="text-success small pt-1 fw-bold">Active</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Categories Card -->
                    <div class="col-xxl-4 col-md-6">
                        <a href="categories.php">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title">Categories</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-grid"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6><?php echo $stats['total_categories']; ?></h6>
                                            <span class="text-muted small pt-1">Categories</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Featured Products Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card">
                            <div class="card-body">
                                <h5 class="card-title">Featured</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background: #1cc88a;">
                                        <i class="bi bi-star"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6><?php echo $stats['featured_products']; ?></h6>
                                        <span class="text-success small pt-1">Products</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Products -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">
                            <div class="card-body">
                                <h5 class="card-title">Recent Products <span>| Added</span></h5>

                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th scope="col">Product</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">Price (USD)</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recent_products)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                                    <p class="text-muted mt-2">No products yet</p>
                                                    <a href="add_product.php" class="btn btn-primary">
                                                        <i class="bi bi-plus-circle"></i> Add First Product
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recent_products as $product): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($product['title']); ?></strong>
                                                        <?php if ($product['company_name']): ?>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($product['company_name']); ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                                    <td>
                                                        <?php if ($product['is_price_visible']): ?>
                                                            $<?php echo number_format($product['price'], 2); ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Hidden</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($product['is_featured']): ?>
                                                            <span class="badge bg-success"><i class="bi bi-star"></i> Featured</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Regular</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>

                    <!-- Category Distribution -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Products by Category</h5>

                                <?php if (empty($category_stats)): ?>
                                    <div class="text-center py-4">
                                        <i class="bi bi-grid" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">No categories yet</p>
                                        <a href="categories.php" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Add Categories
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="row">
                                        <?php foreach ($category_stats as $cat): ?>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                                                        <div class="progress mt-2" style="height: 20px;">
                                                            <div class="progress-bar" role="progressbar" 
                                                                 style="width: <?php echo min(100, ($cat['product_count'] / max(1, $stats['total_products'])) * 100); ?>%"
                                                                 aria-valuenow="<?php echo $cat['product_count']; ?>" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="<?php echo $stats['total_products']; ?>">
                                                                <?php echo $cat['product_count']; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Quick Actions</h5>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <a href="add_product.php" class="btn btn-primary w-100 py-3">
                                            <i class="bi bi-plus-circle fs-4 d-block mb-2"></i>
                                            Add Product
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="product_list.php" class="btn btn-outline-primary w-100 py-3">
                                            <i class="bi bi-list-ul fs-4 d-block mb-2"></i>
                                            View Products
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="categories.php" class="btn btn-outline-secondary w-100 py-3">
                                            <i class="bi bi-grid fs-4 d-block mb-2"></i>
                                            Categories
                                        </a>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="product_list.php?search=" class="btn btn-outline-info w-100 py-3">
                                            <i class="bi bi-search fs-4 d-block mb-2"></i>
                                            Search
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</main>

<?php
require_once 'include/footer.php';
?>