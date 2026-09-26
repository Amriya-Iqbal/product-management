<?php

require_once "../config/database.php";
require_once "../includes/auth.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");

    exit;
}


/* ==========================================
   GET FORM DATA
========================================== */

$name = trim($_POST["name"] ?? "");

$sku = trim($_POST["sku"] ?? "");

$category_id = $_POST["category_id"] ?? "";

$description = trim(
    $_POST["description"] ?? ""
);

$price = $_POST["price"] ?? 0;

$stock = $_POST["stock"] ?? 0;

$low_stock_limit =
    $_POST["low_stock_limit"] ?? 10;

$status =
    $_POST["status"] ?? "Active";


/* ==========================================
   VALIDATION
========================================== */

if (
    empty($name) ||
    empty($sku) ||
    empty($category_id) ||
    $price === ""
) {

    header(
        "Location: create.php?error=" .
        urlencode(
            "Please fill in all required fields."
        )
    );

    exit;
}


if (!is_numeric($price) || $price < 0) {

    header(
        "Location: create.php?error=" .
        urlencode(
            "Please enter a valid price."
        )
    );

    exit;
}


if (!is_numeric($stock) || $stock < 0) {

    header(
        "Location: create.php?error=" .
        urlencode(
            "Please enter a valid stock quantity."
        )
    );

    exit;
}


/* ==========================================
   CHECK SKU
========================================== */

$check = $pdo->prepare(
    "SELECT id
     FROM products
     WHERE sku = ?"
);

$check->execute([$sku]);


if ($check->fetch()) {

    header(
        "Location: create.php?error=" .
        urlencode(
            "This SKU already exists."
        )
    );

    exit;
}


/* ==========================================
   IMAGE UPLOAD
========================================== */

$imageName = null;


if (
    isset($_FILES["image"])
    &&
    $_FILES["image"]["error"] === UPLOAD_ERR_OK
) {


    $file = $_FILES["image"];


    /* FILE SIZE */

    if ($file["size"] > 2 * 1024 * 1024) {

        header(
            "Location: create.php?error=" .
            urlencode(
                "Image must be less than 2MB."
            )
        );

        exit;
    }


    /* MIME TYPE */

    $allowedTypes = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];


    $mimeType = mime_content_type(
        $file["tmp_name"]
    );


    if (!isset($allowedTypes[$mimeType])) {

        header(
            "Location: create.php?error=" .
            urlencode(
                "Only JPG, PNG and WEBP images are allowed."
            )
        );

        exit;
    }


    /* GENERATE UNIQUE FILE NAME */

    $extension =
        $allowedTypes[$mimeType];


    $imageName =
        uniqid("product_", true)
        . "."
        . $extension;


    $uploadDirectory =
        "../assets/uploads/products/";


    /* CREATE DIRECTORY */

    if (!is_dir($uploadDirectory)) {

        mkdir(
            $uploadDirectory,
            0755,
            true
        );
    }


    $uploadPath =
        $uploadDirectory . $imageName;


    if (
        !move_uploaded_file(
            $file["tmp_name"],
            $uploadPath
        )
    ) {

        header(
            "Location: create.php?error=" .
            urlencode(
                "Failed to upload product image."
            )
        );

        exit;
    }

}


/* ==========================================
   INSERT PRODUCT
========================================== */

$stmt = $pdo->prepare(
    "INSERT INTO products
    (
        category_id,
        name,
        sku,
        description,
        price,
        stock,
        low_stock_limit,
        image,
        status
    )

    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )"
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

    $status

]);


/* ==========================================
   REDIRECT
========================================== */

header(
    "Location: index.php?message=" .
    urlencode(
        "Product added successfully."
    )
);

exit;