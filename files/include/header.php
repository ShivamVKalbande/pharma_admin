<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urban Nest Admin Panel</title>
    
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
            <span class="d-none d-lg-block">Urban Nest Admin</span>
        </a>
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
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="tenant.php">
                <i class="bi bi-building"></i>
                <span>Tenants</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="gallery.php">
                <i class="bi bi-images"></i>
                <span>Gallery</span>
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

.nav-link i {
    font-size: 18px;
    margin-right: 10px;
    color: #4154f1;
}

.nav-link:hover {
    color: #4154f1;
    background: #f6f9ff;
}

.header {
    background: #fff;
    height: 60px;
    box-shadow: 0px 2px 20px rgba(1, 41, 112, 0.1);
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
}
</style>
