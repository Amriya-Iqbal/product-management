<?php

require_once "../config/database.php";
require_once "../includes/auth.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");

    exit;
}


/* ==========================================
   GET DATA
========================================== */

$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

$name = trim($_POST["name"] ?? "");

$sku = trim($_POST["sku"] ?? "");

$category_id =
    $_POST["category_id"] ?? "";

$description =
    trim($_POST["description"] ?? "");

$price =
    $_POST["price"] ?? 0;

$stock =
    $_POST["stock"] ?? 0;

$low_stock_limit =
    $_POST["low_stock_limit"] ?? 10;

$status =
    $_POST["status"] ?? "Active";


/* ==========================================
   VALIDATE ID
========================================== */

if (!$id) {

    header("Location: index.php");

    exit;
}


/* ==========================================
   VALIDATE REQUIRED FIELDS
========================================== */

if (
    empty($name) ||
    empty($sku) ||
    empty($category_id)
) {

    header(
        "Location: edit.php?id=$id&error=" .
        urlencode(
            "Please fill in all required fields."
        )
    );

    exit;
}


if (!is_numeric($price) || $price < 0) {

    header(
        "Location: edit.php?id=$id&error=" .
        urlencode(
            "Please enter a valid price."
        )
    );

    exit;
}


if (!is_numeric($stock) || $stock < 0) {

    header(
        "Location: edit.php?id=$id&error=" .
        urlencode(
            "Please enter a valid stock quantity."
        )
    );

    exit;
}


/* ==========================================
   GET CURRENT PRODUCT
========================================== */

$stmt = $pdo->prepare(
    "SELECT *
     FROM products
     WHERE id = ?"
);

$stmt->execute([$id]);

$product = $stmt->fetch();


if (!$product) {

    header("Location: index.php");

    exit;
}


/* ==========================================
   CHECK DUPLICATE SKU
========================================== */

$stmt = $pdo->prepare(
    "SELECT id
     FROM products
     WHERE sku = ?
     AND id != ?"
);

$stmt->execute([
    $sku,
    $id
]);


if ($stmt->fetch()) {

    header(
        "Location: edit.php?id=$id&error=" .
        urlencode(
            "This SKU is already used by another product."
        )
    );

    exit;
}


/* ==========================================
   CURRENT IMAGE
========================================== */

$imageName =
    $product["image"];


/* ==========================================
   NEW IMAGE UPLOAD
========================================== */

if (
    isset($_FILES["image"])
    &&
    $_FILES["image"]["error"] === UPLOAD_ERR_OK
) {


    $file =
        $_FILES["image"];


    /* FILE SIZE */

    if ($file["size"] > 2 * 1024 * 1024) {

        header(
            "Location: edit.php?id=$id&error=" .
            urlencode(
                "Image must be less than 2MB."
            )
        );

        exit;
    }


    /* MIME */

    $allowedTypes = [

        "image/jpeg" => "jpg",

        "image/png" => "png",

        "image/webp" => "webp"

    ];


    $mimeType =
        mime_content_type(
            $file["tmp_name"]
        );


    if (!isset($allowedTypes[$mimeType])) {

        header(
            "Location: edit.php?id=$id&error=" .
            urlencode(
                "Only JPG, PNG and WEBP images are allowed."
            )
        );

        exit;
    }


    $extension =
        $allowedTypes[$mimeType];


    $newImageName =
        uniqid(
            "product_",
            true
        )
        . "."
        . $extension;


    $uploadDirectory =
        "../assets/uploads/products/";


    if (!is_dir($uploadDirectory)) {

        mkdir(
            $uploadDirectory,
            0755,
            true
        );
    }


    $uploadPath =
        $uploadDirectory
        . $newImageName;


    if (
        move_uploaded_file(
            $file["tmp_name"],
            $uploadPath
        )
    ) {


        /* DELETE OLD IMAGE */

        if (
            !empty($product["image"])
            &&
            file_exists(
                $uploadDirectory
                . $product["image"]
            )
        ) {

            unlink(
                $uploadDirectory
                . $product["image"]
            );

        }


        $imageName =
            $newImageName;

    }

}


/* ==========================================
   UPDATE PRODUCT
========================================== */

$stmt = $pdo->prepare(
    "UPDATE products

     SET

        category_id = ?,

        name = ?,

        sku = ?,

        description = ?,

        price = ?,

        stock = ?,

        low_stock_limit = ?,

        image = ?,

        status = ?

     WHERE id = ?"
);


$stmt->execute([

    $category_id,

    $name,

    $sku,

    $description,

    $price,

    $stock,

    $low_stock_limit,

    $imageName,

    $status,

    $id

]);


/* ==========================================
   REDIRECT
========================================== */

header(
    "Location: index.php?message=" .
    urlencode(
        "Product updated successfully."
    )
);

exit;