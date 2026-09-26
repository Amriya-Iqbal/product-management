<?php
require_once "../config/database.php";
require_once "../includes/auth.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = "Invalid category.";
    header("Location: index.php");
    exit;
}

/* Check category exists */
$stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    $_SESSION['error'] = "Category not found.";
    header("Location: index.php");
    exit;
}

/* Check products using this category */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
$stmt->execute([$id]);
$productCount = (int)$stmt->fetchColumn();

if ($productCount > 0) {
    $_SESSION['error'] = "Cannot delete '{$category['name']}' because it contains {$productCount} product(s).";
    header("Location: index.php");
    exit;
}

/* Delete category */
try {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['success'] = "Category deleted successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Unable to delete category. Please try again.";
}

header("Location: index.php");
exit;