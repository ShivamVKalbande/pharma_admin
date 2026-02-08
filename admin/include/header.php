<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>Pharma Admin Panel</title>
    
    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    
    <!-- Bootstrap CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Header -->
<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
        <a href="dashboard.php" class="logo d-flex align-items-center">
            <i class="bi bi-capsule-pill text-primary me-2" style="font-size: 1.8rem;"></i>
            <span class="d-none d-lg-block">Pharma Admin</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo InputValidator::escapeOutput(SecureSession::getUsername()); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><?php echo InputValidator::escapeOutput(SecureSession::getUsername()); ?></h6>
                        <span>Administrator</span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="dashboard.php">
                            <i class="bi bi-house"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<!-- Sidebar -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? '' : 'collapsed'; ?>" href="dashboard.php">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Products Section -->
        <li class="nav-heading">Products Management</li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add_product.php') ? '' : 'collapsed'; ?>" href="add_product.php">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Add Product</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'product_list.php') ? '' : 'collapsed'; ?>" href="product_list.php">
                <i class="bi bi-capsule"></i>
                <span>Product List</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? '' : 'collapsed'; ?>" href="categories.php">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span>Categories</span>
            </a>
        </li>

        <!-- Inventory Section -->
        <li class="nav-heading">Inventory</li>
        
        <li class="nav-item">
            <a class="nav-link collapsed" href="product_list.php?stock=low">
                <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                <span>Low Stock Items</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="product_list.php?stock=out">
                <i class="bi bi-x-circle-fill text-danger"></i>
                <span>Out of Stock</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="product_list.php?prescription=1">
                <i class="bi bi-prescription2"></i>
                <span>Rx Products</span>
            </a>
        </li>

        <!-- Settings Section -->
        <li class="nav-heading">System</li>
        
        <li class="nav-item">
            <a class="nav-link collapsed" href="test_config.php" target="_blank">
                <i class="bi bi-gear-fill"></i>
                <span>System Check</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>

    </ul>
</aside>

<style>
.sidebar {
    position: fixed;
    top: 60px;
    left: 0;
    bottom: 0;
    width: 260px;
    z-index: 996;
    transition: all 0.3s;
    padding: 20px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #aab7cf transparent;
    box-shadow: 0px 0px 20px rgba(1, 41, 112, 0.1);
    background-color: #fff;
}

.sidebar-nav {
    padding: 0;
    margin: 0;
    list-style: none;
}

.nav-heading {
    font-size: 11px;
    text-transform: uppercase;
    color: #899bbd;
    font-weight: 600;
    margin-top: 15px;
    margin-bottom: 5px;
    padding-left: 10px;
}

.nav-link {
    display: flex;
    align-items: center;
    font-size: 15px;
    font-weight: 600;
    color: #012970;
    transition: 0.3s;
    background: #f6f9ff;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 5px;
}

.nav-link.collapsed {
    color: #012970;
    background: #fff;
}

.nav-link i {
    font-size: 18px;
    margin-right: 10px;
    color: #4154f1;
}

.nav-link:hover {
    color: #4154f1;
    background: #f6f9ff;
}

.nav-link:not(.collapsed) {
    color: #4154f1;
    background: #f6f9ff;
}

.header {
    background: #fff;
    height: 60px;
    box-shadow: 0px 2px 20px rgba(1, 41, 112, 0.1);
}

.logo {
    font-size: 26px;
    font-weight: 700;
    color: #012970;
    text-decoration: none;
}

.toggle-sidebar-btn {
    font-size: 32px;
    padding-left: 10px;
    cursor: pointer;
    color: #012970;
}

main {
    margin-top: 60px;
    margin-left: 260px;
    padding: 20px 30px;
    transition: all 0.3s;
}

@media (max-width: 1199px) {
    .sidebar {
        left: -300px;
    }
    main {
        margin-left: 0;
    }
    .toggle-sidebar-btn {
        display: block;
    }
}

@media (min-width: 1200px) {
    .toggle-sidebar-btn {
        display: none;
    }
}
</style>