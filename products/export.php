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
   PRODUCT QUERY
========================= */

$sql = "
    SELECT
        p.id,
        p.name,
        p.sku,
        c.name AS category_name,
        p.price,
        p.stock,
        p.low_stock_limit,
        p.status,
        p.created_at
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE 1=1
";

$params = [];

/* Search */

if ($search !== '') {
    $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

/* Category */

if ($category !== '' && is_numeric($category)) {
    $sql .= " AND p.category_id = ?";
    $params[] = (int)$category;
}

/* Status */

if ($status !== '' && in_array($status, ['Active', 'Inactive'], true)) {
    $sql .= " AND p.status = ?";
    $params[] = $status;
}

/* Low Stock */

if ($stock === 'low') {
    $sql .= " AND p.stock <= p.low_stock_limit";
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   CSV FILE
========================= */

$filename = 'products_' . date('Y-m-d_H-i-s') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

/* =========================
   OUTPUT
========================= */

$output = fopen('php://output', 'w');

/* UTF-8 BOM for Excel */

fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

/* CSV Header */

fputcsv($output, [
    'ID',
    'Product Name',
    'SKU',
    'Category',
    'Price',
    'Stock',
    'Low Stock Limit',
    'Stock Status',
    'Status',
    'Created At'
]);

/* CSV Data */

foreach ($products as $product) {

    $stockStatus = (
        (int)$product['stock'] <=
        (int)$product['low_stock_limit']
    ) ? 'Low Stock' : 'Normal';

    fputcsv($output, [
        $product['id'],
        $product['name'],
        $product['sku'],
        $product['category_name'] ?? 'No Category',
        number_format((float)$product['price'], 2, '.', ''),
        $product['stock'],
        $product['low_stock_limit'],
        $stockStatus,
        $product['status'],
        $product['created_at']
    ]);
}

fclose($output);
exit;