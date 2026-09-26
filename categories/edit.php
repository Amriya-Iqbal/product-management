<?php
require_once "../config/database.php";
require_once "../includes/auth.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = "Invalid category.";
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    $_SESSION['error'] = "Category not found.";
    header("Location: index.php");
    exit;
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$name = $_SESSION['old_name'] ?? $category['name'];
unset($_SESSION['old_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category | StockMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link
    rel="stylesheet"
    href="../assets/css/categories.css"
>
</head>
<body>
<div class="admin-wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon"><i class="bi bi-box-seam"></i></div>
            <div>
                <h5>StockMaster</h5>
                <span>Admin Panel</span>
            </div>
        </div>

        <nav class="sidebar-menu">
            <p class="menu-title">MAIN MENU</p>

            <a href="../dashboard.php" class="menu-item">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <a href="../products/index.php" class="menu-item">
                <i class="bi bi-box-seam"></i>
                <span>Products</span>
            </a>

            <a href="index.php" class="menu-item active">
                <i class="bi bi-tags-fill"></i>
                <span>Categories</span>
            </a>

            <p class="menu-title mt-4">ACCOUNT</p>

            <a href="../logout.php" class="menu-item logout-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">

        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="page-heading">
                <h4>Edit Category</h4>
                <p>Update your product category</p>
            </div>

            <div class="admin-profile">
                <div class="admin-avatar">
                    <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="admin-info">
                    <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></strong>
                    <span>Administrator</span>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-area">

            <div class="content-header">
                <div>
                    <h5>Edit Category</h5>
                    <p>Change the information for this category.</p>
                </div>

                <a href="index.php" class="btn btn-light border">
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Categories
                </a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <?= htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="form-card">

                <form action="update.php" method="POST">

                    <input type="hidden" name="id" value="<?= $category['id']; ?>">

                    <div class="form-section">

                        <div class="form-section-title">
                            <div class="section-icon">
                                <i class="bi bi-pencil-square"></i>
                            </div>

                            <div>
                                <h6>Category Information</h6>
                                <p>Update the category name below.</p>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-8">

                                <label for="name" class="form-label">
                                    Category Name
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control"
                                    value="<?= htmlspecialchars($name); ?>"
                                    maxlength="100"
                                    required
                                    autofocus
                                >

                                <div class="form-text">
                                    Category name must be unique.
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="category-info-box">

                        <div class="category-info-icon">
                            <i class="bi bi-info-circle"></i>
                        </div>

                        <div>
                            <strong>Category ID: #<?= $category['id']; ?></strong>

                            <p>
                                Products assigned to this category will remain
                                connected after changing the category name.
                            </p>
                        </div>

                    </div>

                    <div class="form-actions">

                        <a href="index.php" class="btn btn-light border">
                            Cancel
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Update Category
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>