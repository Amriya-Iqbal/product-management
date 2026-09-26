
<?php
require_once "../config/database.php";
require_once "../includes/auth.php";

$stmt = $pdo->query("
    SELECT id, name
    FROM categories
    ORDER BY name ASC
");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = $_GET["error"] ?? "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | ProductPro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/product-form.css">
</head>
<body class="product-form-page">

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <aside class="col-md-3 col-lg-2 sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">PM</div>
                <span>Product<span>Pro</span></span>
            </div>

            <div class="sidebar-menu">
                <small>MAIN MENU</small>

                <a href="../dashboard.php">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>

                <a href="index.php" class="active">
                    <i class="bi bi-box-seam"></i>
                    Products
                </a>

                <a href="../categories/index.php">
                    <i class="bi bi-tags"></i>
                    Categories
                </a>

                <small class="mt-4">ACCOUNT</small>

                <a href="../logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 main-content">

            <!-- Top Navbar -->
            <nav class="top-navbar">
                <div>
                    <h5 class="mb-1">Add Product</h5>
                    <small>Create a new inventory product</small>
                </div>

                <div class="admin-profile">
                    <div class="admin-avatar">
                        <?= strtoupper(substr($_SESSION["admin_name"] ?? "A", 0, 1)) ?>
                    </div>

                    <div>
                        <strong><?= htmlspecialchars($_SESSION["admin_name"] ?? "Admin") ?></strong>
                        <small>Administrator</small>
                    </div>
                </div>
            </nav>

            <div class="dashboard-content">

                <!-- Page Header -->
                <div class="product-page-header">
                    <div>
                        <div class="product-breadcrumb">
                            <a href="index.php">Products</a>
                            <i class="bi bi-chevron-right"></i>
                            <span>Add Product</span>
                        </div>

                        <h4>Add New Product</h4>
                        <p>Enter the product details below to add it to your inventory.</p>
                    </div>

                    <a href="index.php" class="btn btn-light border product-back-btn">
                        <i class="bi bi-arrow-left"></i>
                        Back to Products
                    </a>
                </div>

                <!-- Error -->
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show product-alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span><?= htmlspecialchars($error) ?></span>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                        ></button>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form action="store.php" method="POST" enctype="multipart/form-data" id="productForm">

                    <div class="row g-4">

                        <!-- Left Column -->
                        <div class="col-xl-8">

                            <!-- Basic Information -->
                            <div class="content-card product-form-card">

                                <div class="product-card-header">
                                    <div class="product-card-heading">
                                        <div class="product-card-icon">
                                            <i class="bi bi-box-seam"></i>
                                        </div>

                                        <div>
                                            <h5>Basic Information</h5>
                                            <p>Product details and identification</p>
                                        </div>
                                    </div>

                                    <span class="section-number">01</span>
                                </div>

                                <div class="product-card-body">

                                    <div class="row g-4">

                                        <div class="col-md-8">
                                            <label for="productName" class="form-label">
                                                Product Name
                                                <span class="required">*</span>
                                            </label>

                                            <input
                                                type="text"
                                                id="productName"
                                                name="name"
                                                class="form-control product-input"
                                                placeholder="Enter product name"
                                                maxlength="255"
                                                required
                                            >
                                        </div>

                                        <div class="col-md-4">
                                            <label for="sku" class="form-label">
                                                SKU
                                                <span class="required">*</span>
                                            </label>

                                            <input
                                                type="text"
                                                id="sku"
                                                name="sku"
                                                class="form-control product-input"
                                                placeholder="e.g. WH-1001"
                                                maxlength="100"
                                                required
                                            >

                                            <div class="form-help">
                                                <i class="bi bi-info-circle"></i>
                                                Unique product code
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="category" class="form-label">
                                                Category
                                                <span class="required">*</span>
                                            </label>

                                            <select
                                                id="category"
                                                name="category_id"
                                                class="form-select product-input"
                                                required
                                            >
                                                <option value="">Select Category</option>

                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= (int)$category["id"] ?>">
                                                        <?= htmlspecialchars($category["name"]) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="status" class="form-label">
                                                Product Status
                                            </label>

                                            <select
                                                id="status"
                                                name="status"
                                                class="form-select product-input"
                                            >
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                            </select>

                                            <div class="form-help">
                                                <i class="bi bi-eye"></i>
                                                Control product visibility
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="description" class="form-label">
                                                Description
                                            </label>

                                            <textarea
                                                id="description"
                                                name="description"
                                                class="form-control product-input product-textarea"
                                                rows="5"
                                                maxlength="2000"
                                                placeholder="Write a short description about this product..."
                                            ></textarea>

                                            <div class="form-help">
                                                Add useful information about the product.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Pricing & Inventory -->
                            <div class="content-card product-form-card mt-4">

                                <div class="product-card-header">
                                    <div class="product-card-heading">
                                        <div class="product-card-icon">
                                            <i class="bi bi-graph-up-arrow"></i>
                                        </div>

                                        <div>
                                            <h5>Pricing & Inventory</h5>
                                            <p>Set product price and stock information</p>
                                        </div>
                                    </div>

                                    <span class="section-number">02</span>
                                </div>

                                <div class="product-card-body">

                                    <div class="row g-4">

                                        <div class="col-md-4">
                                            <label for="price" class="form-label">
                                                Price
                                                <span class="required">*</span>
                                            </label>

                                            <div class="price-field">
                                                <span>Rs.</span>

                                                <input
                                                    type="number"
                                                    id="price"
                                                    name="price"
                                                    class="form-control product-input"
                                                    placeholder="0.00"
                                                    step="0.01"
                                                    min="0"
                                                    required
                                                >
                                            </div>

                                            <div class="form-help">
                                                Product selling price
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="stock" class="form-label">
                                                Stock Quantity
                                                <span class="required">*</span>
                                            </label>

                                            <input
                                                type="number"
                                                id="stock"
                                                name="stock"
                                                class="form-control product-input"
                                                placeholder="0"
                                                min="0"
                                                required
                                            >

                                            <div class="form-help">
                                                Current available quantity
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="lowStockLimit" class="form-label">
                                                Low Stock Limit
                                            </label>

                                            <input
                                                type="number"
                                                id="lowStockLimit"
                                                name="low_stock_limit"
                                                class="form-control product-input"
                                                value="10"
                                                min="0"
                                            >

                                            <div class="form-help">
                                                Alert threshold
                                            </div>
                                        </div>

                                    </div>

                                    <div class="stock-info-box">
                                        <div class="stock-info-icon">
                                            <i class="bi bi-bar-chart-line"></i>
                                        </div>

                                        <div>
                                            <strong>Stock monitoring</strong>
                                            <p>
                                                The system will identify this product as low stock
                                                when the quantity reaches the selected limit.
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- Right Column -->
                        <div class="col-xl-4">

                            <!-- Product Image -->
                            <div class="content-card product-form-card">

                                <div class="product-card-header">
                                    <div class="product-card-heading">
                                        <div class="product-card-icon">
                                            <i class="bi bi-image"></i>
                                        </div>

                                        <div>
                                            <h5>Product Image</h5>
                                            <p>Upload a product photo</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="product-card-body">

                                    <label for="productImage" class="image-upload-area" id="imageUploadArea">

                                        <div id="imagePlaceholder" class="image-placeholder">
                                            <div class="image-upload-icon">
                                                <i class="bi bi-cloud-arrow-up"></i>
                                            </div>

                                            <h6>Upload Product Image</h6>

                                            <p>Click here to choose an image</p>

                                            <span>JPG, PNG or WEBP · Max 2MB</span>
                                        </div>

                                        <img
                                            id="imagePreview"
                                            class="image-preview"
                                            src=""
                                            alt="Product preview"
                                        >

                                        <input
                                            type="file"
                                            name="image"
                                            id="productImage"
                                            class="product-upload-input"
                                            accept=".jpg,.jpeg,.png,.webp"
                                        >
                                    </label>

                                    <div class="image-upload-note">
                                        <i class="bi bi-info-circle"></i>
                                        Recommended image size: 800 × 800 pixels.
                                    </div>

                                </div>
                            </div>

                            <!-- Status Info -->
                            <div class="content-card product-form-card mt-4">

                                <div class="product-card-header">
                                    <div class="product-card-heading">
                                        <div class="product-card-icon">
                                            <i class="bi bi-check2-circle"></i>
                                        </div>

                                        <div>
                                            <h5>Product Status</h5>
                                            <p>Inventory visibility</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="product-card-body">

                                    <div class="status-info">
                                        <div class="status-info-icon">
                                            <i class="bi bi-check-lg"></i>
                                        </div>

                                        <div>
                                            <strong>Active Product</strong>
                                            <p>
                                                Active products can be displayed and managed
                                                throughout your inventory.
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Actions -->
                    <div class="product-form-actions">

                        <a href="index.php" class="btn btn-light border">
                            Cancel
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i>
                            Save Product
                        </button>

                    </div>

                </form>

            </div>
        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const imageInput = document.getElementById("productImage");
    const imagePreview = document.getElementById("imagePreview");
    const imagePlaceholder = document.getElementById("imagePlaceholder");
    const imageUploadArea = document.getElementById("imageUploadArea");

    imageInput.addEventListener("change", function () {
        const file = this.files[0];

        if (!file) {
            return;
        }

        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/webp"
        ];

        if (!allowedTypes.includes(file.type)) {
            alert("Please select a JPG, PNG or WEBP image.");
            this.value = "";
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert("Image size must be less than 2MB.");
            this.value = "";
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            imagePreview.src = event.target.result;
            imagePreview.style.display = "block";
            imagePlaceholder.style.display = "none";
            imageUploadArea.classList.add("has-image");
        };

        reader.readAsDataURL(file);
    });
});
</script>

</body>
</html>

