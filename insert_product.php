<?php
include("config.php"); // assumes $db (PDO) is defined

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get product data
    $name_en = $_POST['name_en'];
    $name_ar = $_POST['name_ar'];
    $desc_en = $_POST['description_en'];
    $desc_ar = $_POST['description_ar'];
    $price = $_POST['price'];
    $seller_id = $_POST['seller_id'];
    $status = $_POST['status'];

    // Insert product
    $stmt = $db->prepare("INSERT INTO products (name_en, name_ar, description_en, description_ar, price, seller_id, status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name_en, $name_ar, $desc_en, $desc_ar, $price, $seller_id, $status]);

    // Get inserted product ID
    $product_id = $db->lastInsertId();

    // Handle image uploads
    if (!empty($_FILES['images']['name'][0])) {
        $alt_texts = explode(',', $_POST['alt_texts']);
        $image_types = explode(',', $_POST['image_types']);

        foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['images']['error'][$index] === 0) {
                $originalName = basename($_FILES['images']['name'][$index]);
                $targetDir = "products";
                $filename = uniqid() . "_" . $originalName;
                $targetFile = $targetDir . $filename;

                // Move the uploaded file
                if (move_uploaded_file($tmpName, $targetFile)) {
                    $url = $targetFile;
                    $alt = $alt_texts[$index] ?? '';
                    // $type = $image_types[$index] ?? 'other';
                    $type= "product";

                    $stmtImg = $db->prepare("INSERT INTO images (url, alt_text, product_id, seller_id, image_type) 
                                             VALUES (?, ?, ?, ?, ?)");
                    $stmtImg->execute([$url, $alt, $product_id, $seller_id, $type]);
                }
            }
        }
    }

    echo "Product and images added successfully.";
    echo '<script>
    setTimeout(function() {
        window.location.href = "store.php";
    }, 3000);
</script>';
} else {
    echo "Invalid request.";
}
