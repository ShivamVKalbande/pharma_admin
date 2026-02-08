<?php
require_once 'pharma_config.php';
require_once 'db.php';
require_once 'SecureSession.php';
require_once 'InputValidator.php';

SecureSession::init();
SecureSession::requireLogin();

$db = getDB();
$errors = [];
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if (!isset($_POST['csrf_token']) || !SecureSession::validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = "Invalid security token";
    } else {
        
        if ($_POST['action'] === 'add_category') {
            $name = InputValidator::sanitizeString($_POST['name'] ?? '');
            $description = InputValidator::sanitizeString($_POST['description'] ?? '');
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            if (empty($name)) {
                $errors[] = "Category name is required";
            }
            
            if (empty($errors)) {
                try {
                    $query = "INSERT INTO categories (name, description, slug, is_active) 
                             VALUES (:name, :description, :slug, :is_active)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([
                        ':name' => $name,
                        ':description' => $description,
                        ':slug' => $slug,
                        ':is_active' => $is_active
                    ]);
                    
                    $success = "Category added successfully!";
                    $_POST = [];
                    
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $errors[] = "Category name already exists";
                    } else {
                        $errors[] = "Database error: " . $e->getMessage();
                    }
                }
            }
        }
        
        elseif ($_POST['action'] === 'delete_category') {
            $category_id = InputValidator::sanitizeInt($_POST['category_id'] ?? '');
            
            if ($category_id > 0) {
                try {
                    // Check if category has products
                    $checkQuery = "SELECT COUNT(*) as count FROM products WHERE category_id = :category_id";
                    $checkStmt = $db->prepare($checkQuery);
                    $checkStmt->execute([':category_id' => $category_id]);
                    $count = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
                    
                    if ($count > 0) {
                        $errors[] = "Cannot delete category with existing products. Please move or delete the products first.";
                    } else {
                        $deleteQuery = "DELETE FROM categories WHERE id = :id";
                        $deleteStmt = $db->prepare($deleteQuery);
                        $deleteStmt->execute([':id' => $category_id]);
                        $success = "Category deleted successfully!";
                    }
                    
                } catch (PDOException $e) {
                    $errors[] = "Failed to delete category";
                }
            }
        }
    }
}

// Get all categories
$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
          FROM categories c
          ORDER BY c.display_order, c.name";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Category Management";
require_once 'include/header.php';
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Category Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Categories</li>
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
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4">
                <!-- Add Category Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add New Category</h5>

                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo SecureSession::generateCSRFToken(); ?>">
                            <input type="hidden" name="action" value="add_category">

                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           <?php echo (!isset($_POST['action']) || isset($_POST['is_active'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Category
                            </button>
                        </form>

                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Categories List Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Categories List</h5>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Products</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($categories)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No categories found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                                    <?php if ($category['description']): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($category['description'], 0, 50)); ?><?php echo strlen($category['description']) > 50 ? '...' : ''; ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><code><?php echo htmlspecialchars($category['slug']); ?></code></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $category['product_count']; ?> products</span>
                                                </td>
                                                <td>
                                                    <?php if ($category['is_active']): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                        <input type="hidden" name="csrf_token" 
                                                               value="<?php echo SecureSession::generateCSRFToken(); ?>">
                                                        <input type="hidden" name="action" value="delete_category">
                                                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                <?php echo ($category['product_count'] > 0) ? 'disabled title="Cannot delete category with products"' : ''; ?>>
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

                    </div>
                </div>
            </div>
        </div>

    </section>
</main>

<?php
require_once 'include/footer.php';
?>
