<?php
require_once "../config/database.php";
require_once "../includes/auth.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$name = trim($_POST['name'] ?? '');

$_SESSION['old_name'] = $name;

if ($name === '') {
    $_SESSION['error'] = "Category name is required.";
    header("Location: create.php");
    exit;
}

if (strlen($name) > 100) {
    $_SESSION['error'] = "Category name cannot exceed 100 characters.";
    header("Location: create.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM categories WHERE LOWER(name) = LOWER(?) LIMIT 1");
$stmt->execute([$name]);

if ($stmt->fetch()) {
    $_SESSION['error'] = "This category already exists.";
    header("Location: create.php");
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO categories (name, created_at) VALUES (?, NOW())");
    $stmt->execute([$name]);

    unset($_SESSION['old_name']);

    $_SESSION['success'] = "Category created successfully.";
    header("Location: index.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Unable to create category. Please try again.";
    header("Location: create.php");
    exit;
}