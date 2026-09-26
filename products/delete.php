<?php

require_once "../config/database.php";
require_once "../includes/auth.php";


/* ==========================================
   GET PRODUCT ID
========================================== */

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);


if (!$id) {

    header("Location: index.php");

    exit;
}


/* ==========================================
   GET PRODUCT
========================================== */

$stmt = $pdo->prepare(
    "SELECT image
     FROM products
     WHERE id = ?"
);

$stmt->execute([$id]);

$product = $stmt->fetch();


if (!$product) {

    header(
        "Location: index.php?message=" .
        urlencode("Product not found.")
    );

    exit;
}


/* ==========================================
   DELETE IMAGE
========================================== */

if (!empty($product["image"])) {


    $imagePath =
        "../assets/uploads/products/"
        . $product["image"];


    if (file_exists($imagePath)) {

        unlink($imagePath);

    }

}


/* ==========================================
   DELETE PRODUCT
========================================== */

$stmt = $pdo->prepare(
    "DELETE FROM products
     WHERE id = ?"
);

$stmt->execute([$id]);


/* ==========================================
   REDIRECT
========================================== */

header(
    "Location: index.php?message=" .
    urlencode(
        "Product deleted successfully."
    )
);

exit;