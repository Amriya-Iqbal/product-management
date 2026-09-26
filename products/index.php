<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

/* =========================
   FILTER VALUES
========================= */

$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';
$stock = $_GET['stock'] ?? '';

/* =========================
   PAGINATION
========================= */

$perPage = 10;

$page = isset($_GET['page']) && is_numeric($_GET['page'])
    ? (int)$_GET['page']
    : 1;

$page = max($page, 1);

/* =========================
   BASE QUERY
========================= */

$where = "WHERE 1=1";
$params = [];

/* Search */

if ($search !== '') {
    $where .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

/* Category */

if ($category !== '' && is_numeric($category)) {
    $where .= " AND p.category_id = ?";
    $params[] = (int)$category;
}

/* Status */

if ($status !== '' && in_array($status, ['Active', 'Inactive'], true)) {
    $where .= " AND p.status = ?";
    $params[] = $status;
}

/* Low Stock */

if ($stock === 'low') {
    $where .= " AND p.stock <= p.low_stock_limit";
}

/* =========================
   TOTAL PRODUCTS
========================= */

$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM products p
    $where
");

$countStmt->execute($params);

$totalProducts = (int)$countStmt->fetchColumn();

$totalPages = max(
    1,
    (int)ceil($totalProducts / $perPage)
);

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

/* =========================
   PRODUCT QUERY
========================= */

$sql = "
    SELECT
        p.*,
        c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    $where
    ORDER BY p.id DESC
    LIMIT $perPage OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   CATEGORY LIST
========================= */

$categoryStmt = $pdo->query("
    SELECT id, name
    FROM categories
    ORDER BY name ASC
");

$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FLASH MESSAGES
========================= */

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['success'], $_SESSION['error']);

/* =========================
   FILTER STATE
========================= */

$filterActive = (
    $search !== '' ||
    $category !== '' ||
    $status !== '' ||
    $stock !== ''
);
?>

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products - Product Management</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Common CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Product CSS -->
    <link rel="stylesheet" href="../assets/css/products.css">
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

            <a href="../dashboard.php" class="menu-item">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <a href="index.php" class="menu-item active">
                <i class="bi bi-box-seam"></i>
                <span>Products</span>
            </a>

            <a href="../categories/index.php" class="menu-item">
                <i class="bi bi-tags"></i>
                <span>Categories</span>
            </a>

            <div class="menu-title mt-4">
                ACCOUNT
            </div>

            <a href="../logout.php" class="menu-item logout-link">
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

                <h4>Products</h4>

                <p>
                    Manage your products and inventory
                </p>

            </div>

            <div class="admin-profile">

                <div class="admin-avatar">
                    <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                </div>

                <div class="admin-info">

                    <strong>
                        <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
                    </strong>

                    <span>Administrator</span>

                </div>

            </div>

        </header>

        <!-- =========================
             CONTENT AREA
        ========================== -->

        <section class="content-area">

            <!-- PAGE HEADER -->

            <div class="content-header">

                <div>

                    <h5>
                        Product List
                    </h5>

                    <p>
                        View, search and manage all products
                    </p>

                </div>

                <div class="d-flex gap-2">

                    <a
                        href="export.php<?= $filterActive ? '?' . http_build_query($_GET) : '' ?>"
                        class="btn btn-light"
                    >
                        <i class="bi bi-download me-1"></i>
                        Export CSV
                    </a>

                    <a
                        href="create.php"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-plus-lg me-1"></i>
                        Add Product
                    </a>

                </div>

            </div>

            <!-- =========================
                 ALERTS
            ========================== -->

            <?php if ($success): ?>

                <div class="alert alert-success alert-dismissible fade show" role="alert">

                    <i class="bi bi-check-circle me-2"></i>

                    <?= htmlspecialchars($success) ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            <?php endif; ?>

            <?php if ($error): ?>

                <div class="alert alert-danger alert-dismissible fade show" role="alert">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    <?= htmlspecialchars($error) ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            <?php endif; ?>

            <!-- =========================
                 FILTER CARD
            ========================== -->

            <div class="filter-card">

                <form method="GET" action="index.php">

                    <div class="row g-3">

                        <!-- SEARCH -->

                        <div class="col-lg-4">

                            <label class="form-label">
                                Search
                            </label>

                            <div class="search-box">

                                <i class="bi bi-search"></i>

                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="Search product or SKU..."
                                    value="<?= htmlspecialchars($search) ?>"
                                >

                            </div>

                        </div>

                        <!-- CATEGORY -->

                        <div class="col-lg-3">

                            <label class="form-label">
                                Category
                            </label>

                            <select
                                name="category"
                                class="form-select"
                            >

                                <option value="">
                                    All Categories
                                </option>

                                <?php foreach ($categories as $cat): ?>

                                    <option
                                        value="<?= $cat['id'] ?>"
                                        <?= ($category == $cat['id']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <!-- STATUS -->

                        <div class="col-lg-2">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                name="status"
                                class="form-select"
                            >

                                <option value="">
                                    All Status
                                </option>

                                <option
                                    value="Active"
                                    <?= ($status === 'Active') ? 'selected' : '' ?>
                                >
                                    Active
                                </option>

                                <option
                                    value="Inactive"
                                    <?= ($status === 'Inactive') ? 'selected' : '' ?>
                                >
                                    Inactive
                                </option>

                            </select>

                        </div>

                        <!-- STOCK -->

                        <div class="col-lg-2">

                            <label class="form-label">
                                Stock
                            </label>

                            <select
                                name="stock"
                                class="form-select"
                            >

                                <option value="">
                                    All Stock
                                </option>

                                <option
                                    value="low"
                                    <?= ($stock === 'low') ? 'selected' : '' ?>
                                >
                                    Low Stock
                                </option>

                            </select>

                        </div>

                        <!-- SEARCH BUTTON -->

                        <div class="col-lg-1 d-flex align-items-end">

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                                title="Apply Filters"
                            >
                                <i class="bi bi-search"></i>
                            </button>

                        </div>

                    </div>

                </form>

                <!-- CLEAR FILTERS -->

                <?php if ($filterActive): ?>

                    <div class="mt-3">

                        <a
                            href="index.php"
                            class="btn btn-light btn-sm"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            Clear Filters
                        </a>

                    </div>

                <?php endif; ?>

            </div>

            <!-- =========================
                 PRODUCT TABLE
            ========================== -->

            <div class="table-card">

                <div class="table-card-header d-flex justify-content-between align-items-center">

                    <div>

                        <h5>
                            Products
                        </h5>

                        <span>
                            <?= number_format($totalProducts) ?> product(s) found
                        </span>

                    </div>

                    <?php if ($stock === 'low'): ?>

                        <span class="badge bg-danger-subtle text-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Low Stock
                        </span>

                    <?php endif; ?>

                </div>

                <div class="table-responsive">

                    <table class="table custom-table products-table align-middle mb-0">

                        <thead>

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th>
                                    SKU
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Price
                                </th>

                                <th>
                                    Stock
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (count($products) > 0): ?>

                                <?php foreach ($products as $product): ?>

                                    <tr>

                                        <!-- PRODUCT -->

                                        <td>

                                            <div class="product-info">

                                                <?php if (!empty($product['image'])): ?>

                                                    <img
                                                        src="../assets/uploads/products/<?= htmlspecialchars($product['image']) ?>"
                                                        alt="<?= htmlspecialchars($product['name']) ?>"
                                                        class="product-image"
                                                    >

                                                <?php else: ?>

                                                    <div class="product-image-placeholder">
                                                        <i class="bi bi-image"></i>
                                                    </div>

                                                <?php endif; ?>

                                                <div class="product-details">

                                                    <strong>
                                                        <?= htmlspecialchars($product['name']) ?>
                                                    </strong>

                                                    <span>
                                                        ID #<?= $product['id'] ?>
                                                    </span>

                                                </div>

                                            </div>

                                        </td>

                                        <!-- SKU -->

                                        <td>

                                            <span class="sku-text">
                                                <?= htmlspecialchars($product['sku']) ?>
                                            </span>

                                        </td>

                                        <!-- CATEGORY -->

                                        <td>

                                            <?php if (!empty($product['category_name'])): ?>

                                                <span class="category-badge">
                                                    <?= htmlspecialchars($product['category_name']) ?>
                                                </span>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    No Category
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <!-- PRICE -->

                                        <td>

                                            <span class="price-text">
                                                Rs. <?= number_format((float)$product['price'], 2) ?>
                                            </span>

                                        </td>

                                        <!-- STOCK -->

                                        <td>

                                            <?php
                                            $isLowStock = (
                                                (int)$product['stock'] <=
                                                (int)$product['low_stock_limit']
                                            );
                                            ?>

                                            <span class="stock-text <?= $isLowStock ? 'stock-low' : 'stock-normal' ?>">

                                                <?= number_format((int)$product['stock']) ?>

                                                <?php if ($isLowStock): ?>

                                                    <i
                                                        class="bi bi-exclamation-circle ms-1"
                                                        title="Low Stock"
                                                    ></i>

                                                <?php endif; ?>

                                            </span>

                                        </td>

                                        <!-- STATUS -->

                                        <td>

                                            <?php if ($product['status'] === 'Active'): ?>

                                                <span class="status-badge active">

                                                    <i class="bi bi-check-circle-fill"></i>

                                                    Active

                                                </span>

                                            <?php else: ?>

                                                <span class="status-badge inactive">

                                                    <i class="bi bi-dash-circle"></i>

                                                    Inactive

                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <!-- ACTION -->

                                        <td class="text-end">

                                            <div class="dropdown">

                                                <button
                                                    class="btn btn-light product-action-btn"
                                                    type="button"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false"
                                                >
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end">

                                                    <li>

                                                        <a
                                                            class="dropdown-item"
                                                            href="edit.php?id=<?= $product['id'] ?>"
                                                        >
                                                            <i class="bi bi-pencil"></i>
                                                            Edit
                                                        </a>

                                                    </li>

                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>

                                                    <li>

                                                        <button
                                                            type="button"
                                                            class="dropdown-item text-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal"
                                                            data-product-id="<?= $product['id'] ?>"
                                                            data-product-name="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
                                                        >
                                                            <i class="bi bi-trash"></i>
                                                            Delete
                                                        </button>

                                                    </li>

                                                </ul>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <!-- EMPTY STATE -->

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center py-5"
                                    >

                                        <div class="product-empty-icon">

                                            <i class="bi bi-box-seam"></i>

                                        </div>

                                        <h6>
                                            No Products Found
                                        </h6>

                                        <p class="text-muted mb-3">
                                            No products match your current filters.
                                        </p>

                                        <?php if ($filterActive): ?>

                                            <a
                                                href="index.php"
                                                class="btn btn-light btn-sm"
                                            >
                                                <i class="bi bi-x-circle me-1"></i>
                                                Clear Filters
                                            </a>

                                        <?php else: ?>

                                            <a
                                                href="create.php"
                                                class="btn btn-primary btn-sm"
                                            >
                                                <i class="bi bi-plus-lg me-1"></i>
                                                Add Product
                                            </a>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>

<!-- =========================
     DELETE MODAL
========================== -->

<div
    class="modal fade"
    id="deleteModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Delete Product
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body text-center">

                <div class="delete-icon mb-3">

                    <i class="bi bi-trash"></i>

                </div>

                <h6>
                    Are you sure?
                </h6>

                <p class="text-muted mb-0">

                    You are about to delete

                    <strong id="deleteProductName">
                        this product
                    </strong>.

                    <br>

                    This action cannot be undone.

                </p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <a
                    href="#"
                    id="confirmDeleteBtn"
                    class="btn btn-danger"
                >
                    <i class="bi bi-trash me-1"></i>
                    Delete Product
                </a>

            </div>

        </div>

    </div>

</div>

<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Delete Modal Script -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteModal');
    const deleteProductName = document.getElementById('deleteProductName');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        const productId = button.getAttribute('data-product-id');
        const productName = button.getAttribute('data-product-name');

        deleteProductName.textContent = productName;

        confirmDeleteBtn.href = 'delete.php?id=' + productId;
    });
});
</script>

</body>
</html>