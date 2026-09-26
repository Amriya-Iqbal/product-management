<?php
require_once "../config/database.php";
require_once "../includes/auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name = trim($_POST['name'] ?? '');

if ($id <= 0) {
    $_SESSION['error'] = "Invalid category.";
    header("Location: index.php");
    exit;
}

$_SESSION['old_name'] = $name;

if ($name === '') {
    $_SESSION['error'] = "Category name is required.";
    header("Location: edit.php?id=" . $id);
    exit;
}

if (strlen($name) > 100) {
    $_SESSION['error'] = "Category name cannot exceed 100 characters.";
    header("Location: edit.php?id=" . $id);
    exit;
}

/* Check category exists */
$stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    unset($_SESSION['old_name']);
    $_SESSION['error'] = "Category not found.";
    header("Location: index.php");
    exit;
}

/* Check duplicate name */
$stmt = $pdo->prepare("
    SELECT id
    FROM categories
    WHERE LOWER(name) = LOWER(?)
    AND id != ?
    LIMIT 1
");
$stmt->execute([$name, $id]);

if ($stmt->fetch()) {
    $_SESSION['error'] = "Another category with this name already exists.";
    header("Location: edit.php?id=" . $id);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->execute([$name, $id]);

    unset($_SESSION['old_name']);

    $_SESSION['success'] = "Category updated successfully.";
    header("Location: index.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Unable to update category. Please try again.";
    header("Location: edit.php?id=" . $id);
    exit;
}