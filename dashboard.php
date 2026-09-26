<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

/* =========================
   DASHBOARD STATISTICS
========================= */

$totalProducts = (int)$pdo->query("
    SELECT COUNT(*)
    FROM products
")->fetchColumn();

$totalCategories = (int)$pdo->query("
    SELECT COUNT(*)
    FROM categories
")->fetchColumn();

$totalStock = (int)$pdo->query("
    SELECT COALESCE(SUM(stock), 0)
    FROM products
")->fetchColumn();

$lowStockProducts = (int)$pdo->query("
    SELECT COUNT(*)
    FROM products
    WHERE stock <= low_stock_limit
    AND status = 'Active'
")->fetchColumn();

$lowStockValue = (float)$pdo->query("
    SELECT COALESCE(SUM(stock * price), 0)
    FROM products
    WHERE stock <= low_stock_limit
    AND status = 'Active'
")->fetchColumn();

$totalInventoryValue = (float)$pdo->query("
    SELECT COALESCE(SUM(stock * price), 0)
    FROM products
    WHERE status = 'Active'
")->fetchColumn();

/* =========================
   RECENT PRODUCTS
========================= */

$recentProducts = $pdo->query("
    SELECT
        p.id,
        p.name,
        p.sku,
        p.price,
        p.stock,
        p.image,
        c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    ORDER BY p.id DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   LOW STOCK PRODUCTS
========================= */

$lowStockList = $pdo->query("
    SELECT
        p.id,
        p.name,
        p.sku,
        p.stock,
        p.low_stock_limit,
        p.image
    FROM products p
    WHERE p.stock <= p.low_stock_limit
    AND p.status = 'Active'
    ORDER BY p.stock ASC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Product Management</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Common CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>

<div class="admin-wrapper">

    <!-- =========================
         SIDEBAR
    ========================== -->

    <aside class="sidebar">

        <div class="sidebar-logo">
            <div class="logo-icon">
                <i class="bi bi-box-seam"></i>
            </div>

            <div>
                <h5>Product Manager</h5>
                <span>Admin Panel</span>
            </div>
        </div>

        <div class="sidebar-menu">

            <div class="menu-title">
                MAIN MENU
            </div>

            <a href="dashboard.php" class="menu-item active">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <a href="products/index.php" class="menu-item">
                <i class="bi bi-box-seam"></i>
                <span>Products</span>
            </a>

            <a href="categories/index.php" class="menu-item">
                <i class="bi bi-tags"></i>
                <span>Categories</span>
            </a>

            <div class="menu-title mt-4">
                ACCOUNT
            </div>

            <a href="logout.php" class="menu-item logout-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>

        </div>

    </aside>

    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main class="main-content">

        <!-- TOP NAVBAR -->

        <header class="top-navbar">

            <div class="page-heading">
                <h4>Dashboard</h4>
                <p>Overview of your product inventory</p>
            </div>

            <div class="admin-profile">

                <div class="admin-avatar">
                    <?php
                    echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1));
                    ?>
                </div>

                <div class="admin-info">
                    <strong>
                        <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
                    </strong>

                    <span>Administrator</span>
                </div>

            </div>

        </header>

        <!-- CONTENT -->

        <section class="content-area">

            <!-- WELCOME -->

            <div class="dashboard-welcome">
                <h5>
                    Welcome back, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>!
                </h5>

                <p>
                    Here's what's happening with your inventory today.
                </p>
            </div>

            <!-- =========================
                 STAT CARDS
            ========================== -->

            <div class="row g-3 dashboard-stats">

    <!-- TOTAL PRODUCTS -->

    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-card-top">

                <div class="stat-icon blue">
                    <i class="bi bi-box-seam"></i>
                </div>

            </div>

            <div class="stat-label">
                Total Products
            </div>

            <div class="stat-value">
                <?= number_format($totalProducts) ?>
            </div>

        </div>

    </div>

    <!-- TOTAL CATEGORIES -->

    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-card-top">

                <div class="stat-icon green">
                    <i class="bi bi-tags"></i>
                </div>

            </div>

            <div class="stat-label">
                Total Categories
            </div>

            <div class="stat-value">
                <?= number_format($totalCategories) ?>
            </div>

        </div>

    </div>

    <!-- TOTAL STOCK -->

    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-card-top">

                <div class="stat-icon blue">
                    <i class="bi bi-stack"></i>
                </div>

            </div>

            <div class="stat-label">
                Total Stock
            </div>

            <div class="stat-value">
                <?= number_format($totalStock) ?>
            </div>

        </div>

    </div>

    <!-- LOW STOCK -->

    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-card-top">

                <div class="stat-icon orange">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>

            </div>

            <div class="stat-label">
                Low Stock Products
            </div>

            <div class="stat-value">
                <?= number_format($lowStockProducts) ?>
            </div>

        </div>

    </div>

</div>

            <!-- =========================
                 DASHBOARD CONTENT
            ========================== -->

            <div class="row g-3">

                <!-- LOW STOCK PRODUCTS -->

                <div class="col-lg-7">

                    <div class="dashboard-card low-stock-card">

                        <div class="dashboard-card-header">

                            <div>
                                <h6>Low Stock Products</h6>
                                <span>Products that need attention</span>
                            </div>

                            <a href="products/index.php?stock=low"
                               class="btn btn-sm btn-light">
                                View All
                            </a>

                        </div>

                        <div class="dashboard-card-body">

                            <?php if (count($lowStockList) > 0): ?>

                                <?php foreach ($lowStockList as $product): ?>

                                    <div class="low-stock-item">

                                        <div class="low-stock-product">

                                            <?php if (!empty($product['image'])): ?>

                                                <img
                                                    src="assets/uploads/products/<?= htmlspecialchars($product['image']) ?>"
                                                    class="low-stock-image"
                                                    alt="<?= htmlspecialchars($product['name']) ?>"
                                                >

                                            <?php else: ?>

                                                <div class="low-stock-placeholder">
                                                    <i class="bi bi-image"></i>
                                                </div>

                                            <?php endif; ?>

                                            <div class="low-stock-info">

                                                <strong>
                                                    <?= htmlspecialchars($product['name']) ?>
                                                </strong>

                                                <span>
                                                    SKU: <?= htmlspecialchars($product['sku']) ?>
                                                </span>

                                            </div>

                                        </div>

                                        <div class="low-stock-count">
                                            <?= number_format($product['stock']) ?> left
                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <div class="dashboard-empty">

                                    <i class="bi bi-check-circle"></i>

                                    <p>
                                        All products have sufficient stock.
                                    </p>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

                <!-- QUICK ACTIONS -->

                <div class="col-lg-5">

                    <div class="dashboard-card">

                        <div class="dashboard-card-header">

                            <div>
                                <h6>Quick Actions</h6>
                                <span>Manage your inventory</span>
                            </div>

                        </div>

                        <div class="dashboard-card-body">

                            <div class="quick-actions">

                                <a href="products/create.php" class="quick-action">

                                    <i class="bi bi-plus-circle"></i>

                                    <span>
                                        Add Product
                                    </span>

                                </a>

                                <a href="categories/create.php" class="quick-action">

                                    <i class="bi bi-tag"></i>

                                    <span>
                                        Add Category
                                    </span>

                                </a>

                                <a href="products/index.php" class="quick-action">

                                    <i class="bi bi-box-seam"></i>

                                    <span>
                                        View Products
                                    </span>

                                </a>

                                <a href="categories/index.php" class="quick-action">

                                    <i class="bi bi-tags"></i>

                                    <span>
                                        View Categories
                                    </span>

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- =========================
                 RECENT PRODUCTS
            ========================== -->

            <div class="dashboard-card mt-3">

                <div class="dashboard-card-header">

                    <div>
                        <h6>Recent Products</h6>
                        <span>Recently added products</span>
                    </div>

                    <a href="products/index.php"
                       class="btn btn-sm btn-light">
                        View All
                    </a>

                </div>

                <div class="dashboard-card-body">

                    <?php if (count($recentProducts) > 0): ?>

                        <div class="row g-3">

                            <?php foreach ($recentProducts as $product): ?>

                                <div class="col-md-6 col-xl-4">

                                    <div class="recent-product">

                                        <?php if (!empty($product['image'])): ?>

                                            <img
                                                src="assets/uploads/products/<?= htmlspecialchars($product['image']) ?>"
                                                class="recent-product-image"
                                                alt="<?= htmlspecialchars($product['name']) ?>"
                                            >

                                        <?php else: ?>

                                            <div class="recent-product-placeholder">
                                                <i class="bi bi-image"></i>
                                            </div>

                                        <?php endif; ?>

                                        <div class="recent-product-info">

                                            <strong>
                                                <?= htmlspecialchars($product['name']) ?>
                                            </strong>

                                            <span>
                                                <?= htmlspecialchars($product['category_name'] ?? 'No Category') ?>
                                                ·
                                                Rs. <?= number_format($product['price'], 2) ?>
                                            </span>

                                        </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div class="dashboard-empty">

                            <i class="bi bi-box"></i>

                            <p>
                                No products have been added yet.
                            </p>

                            <a href="products/create.php"
                               class="btn btn-primary btn-sm mt-2">
                                Add Product
                            </a>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </section>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>