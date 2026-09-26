
<?php
require_once "../config/database.php";
require_once "../includes/auth.php";

$search = trim($_GET['search'] ?? '');

$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

/* Count total categories */
$countSql = "SELECT COUNT(*) FROM categories c";
$countParams = [];

if ($search !== '') {
    $countSql .= " WHERE c.name LIKE ?";
    $countParams[] = "%" . $search . "%";
}

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($countParams);

$totalCategories = (int)$countStmt->fetchColumn();

$totalPages = max(1, (int)ceil($totalCategories / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

/* Get categories */
$sql = "SELECT
            c.id,
            c.name,
            c.created_at,
            COUNT(p.id) AS product_count
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id";

$params = [];

if ($search !== '') {
    $sql .= " WHERE c.name LIKE ?";
    $params[] = "%" . $search . "%";
}

$sql .= " GROUP BY c.id
          ORDER BY c.id DESC
          LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key + 1, $value, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | StockMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/categories.css">
</head>
<body>

<div class="admin-wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <i class="bi bi-box-seam"></i>
            </div>
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
                <h4>Categories</h4>
                <p>Manage your product categories</p>
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

        <!-- Page Content -->
        <div class="content-area">

            <!-- Alerts -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= htmlspecialchars($success); ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <?= htmlspecialchars($error); ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="content-header">
                <div>
                    <h5>All Categories</h5>
                    <p>Create and manage categories for your products.</p>
                </div>

                <a href="create.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    Add Category
                </a>
            </div>

            <!-- Search -->
            <div class="filter-card">
                <form method="GET" class="row g-2 align-items-center">

                    <div class="col-md-8 col-lg-6">
                        <div class="search-box">
                            <i class="bi bi-search"></i>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search categories..."
                                value="<?= htmlspecialchars($search); ?>"
                            >
                        </div>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>
                    </div>

                    <?php if ($search !== ''): ?>
                        <div class="col-auto">
                            <a href="index.php" class="btn btn-light border">
                                Clear
                            </a>
                        </div>
                    <?php endif; ?>

                </form>
            </div>

            <!-- Category Table -->
            <div class="table-card">

                <div class="table-card-header">
                    <div>
                        <h5>Category List</h5>
                        <span>
                            <?= number_format($totalCategories); ?> categories found
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table custom-table category-table align-middle mb-0">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Products</th>
                                <th>Created Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (count($categories) > 0): ?>

                            <?php foreach ($categories as $index => $category): ?>

                                <tr>

                                    <td>
                                        <?= $offset + $index + 1; ?>
                                    </td>

                                    <td>
                                        <div class="category-name">

                                            <div class="category-icon">
                                                <i class="bi bi-tag"></i>
                                            </div>

                                            <strong>
                                                <?= htmlspecialchars($category['name']); ?>
                                            </strong>

                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= (int)$category['product_count']; ?>
                                            Products
                                        </span>
                                    </td>

                                    <td>
                                        <?= date(
                                            'd M Y',
                                            strtotime($category['created_at'])
                                        ); ?>
                                    </td>

                                    <td class="text-end">

                                        <div class="dropdown">

                                            <button
                                                class="btn btn-sm btn-light border"
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
                                                        href="edit.php?id=<?= (int)$category['id']; ?>"
                                                    >
                                                        <i class="bi bi-pencil me-2"></i>
                                                        Edit
                                                    </a>
                                                </li>

                                                <li>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item text-danger"
                                                        onclick="deleteCategory(
                                                            <?= (int)$category['id']; ?>,
                                                            <?= htmlspecialchars(
                                                                json_encode($category['name']),
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ); ?>
                                                        )"
                                                    >
                                                        <i class="bi bi-trash me-2"></i>
                                                        Delete
                                                    </button>
                                                </li>

                                            </ul>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="5" class="text-center py-5">

                                    <div class="empty-state">

                                        <i class="bi bi-tags"></i>

                                        <h6>No Categories Found</h6>

                                        <p>
                                            <?= $search !== ''
                                                ? 'No categories match your search.'
                                                : 'Start by creating your first category.'; ?>
                                        </p>

                                        <?php if ($search === ''): ?>

                                            <a
                                                href="create.php"
                                                class="btn btn-primary btn-sm"
                                            >
                                                <i class="bi bi-plus-lg me-1"></i>
                                                Add Category
                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </td>
                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>

                    <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">

                        <div class="text-muted small">
                            Showing
                            <?= number_format($offset + 1); ?>
                            -
                            <?= number_format(
                                min(
                                    $offset + $perPage,
                                    $totalCategories
                                )
                            ); ?>
                            of
                            <?= number_format($totalCategories); ?>
                            categories
                        </div>

                        <nav aria-label="Category pagination">

                            <ul class="pagination pagination-sm mb-0">

                                <!-- Previous -->
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">

                                    <?php
                                    $previousParams = $_GET;
                                    $previousParams['page'] = $page - 1;
                                    ?>

                                    <a
                                        class="page-link"
                                        href="?<?= http_build_query($previousParams); ?>"
                                        aria-label="Previous"
                                    >
                                        <i class="bi bi-chevron-left"></i>
                                    </a>

                                </li>

                                <!-- Page Numbers -->
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                                    <?php
                                    $pageParams = $_GET;
                                    $pageParams['page'] = $i;
                                    ?>

                                    <li class="page-item <?= ($i === $page) ? 'active' : ''; ?>">

                                        <a
                                            class="page-link"
                                            href="?<?= http_build_query($pageParams); ?>"
                                        >
                                            <?= $i; ?>
                                        </a>

                                    </li>

                                <?php endfor; ?>

                                <!-- Next -->
                                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">

                                    <?php
                                    $nextParams = $_GET;
                                    $nextParams['page'] = $page + 1;
                                    ?>

                                    <a
                                        class="page-link"
                                        href="?<?= http_build_query($nextParams); ?>"
                                        aria-label="Next"
                                    >
                                        <i class="bi bi-chevron-right"></i>
                                    </a>

                                </li>

                            </ul>

                        </nav>

                    </div>

                <?php endif; ?>

            </div>

        </div>
    </main>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <div class="modal-header">

                <h5 class="modal-title">
                    Delete Category
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body">

                <div class="delete-icon">
                    <i class="bi bi-trash"></i>
                </div>

                <h6 class="text-center mt-3">
                    Are you sure?
                </h6>

                <p class="text-center text-muted mb-0">
                    You are about to delete
                    <strong id="deleteCategoryName"></strong>.
                </p>

                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Categories containing products cannot be deleted.
                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light border"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <a
                    href="#"
                    id="confirmDeleteCategory"
                    class="btn btn-danger"
                >
                    <i class="bi bi-trash me-1"></i>
                    Delete
                </a>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function deleteCategory(id, name) {
    document.getElementById('deleteCategoryName').textContent = name;
    document.getElementById('confirmDeleteCategory').href = 'delete.php?id=' + id;

    const modal = new bootstrap.Modal(
        document.getElementById('deleteCategoryModal')
    );

    modal.show();
}
</script>

</body>
</html>
