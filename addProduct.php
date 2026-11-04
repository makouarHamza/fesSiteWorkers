<?php 
include("config.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // إعادة التوجيه إلى صفحة تسجيل الدخول أو صفحة غير مصرح بها
    header("Location: login.php");
    exit();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    
    try {
        // First, get all images for this product to delete them from server
        $stmt = $db->prepare("SELECT url FROM images WHERE product_id = ?");
        $stmt->execute([$product_id]);
        
        // Delete image files from server
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $image_path = $row['url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete images from database
        $delete_images_stmt = $db->prepare("DELETE FROM images WHERE product_id = ?");
        $delete_images_stmt->execute([$product_id]);
        
        // Delete the product from database
        $delete_product_stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $delete_product_stmt->execute([$product_id]);
        
        echo "<div class='success'>Product and associated images deleted successfully!</div>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>Error deleting product: " . $e->getMessage() . "</div>";
    }
}

// Handle edit request - fetch product data for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $product_id = intval($_GET['edit']);
    
    try {
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$edit_product) {
            echo "<div class='error'>Product not found!</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'>Error fetching product: " . $e->getMessage() . "</div>";
    }
}

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $product_id = intval($_POST['product_id']);
    $title = $_POST['title'];
    $title_ar = $_POST['title_ar'];
    $description_ar = $_POST['description_ar'];
    $description= $_POST['description'];
    $price = $_POST['price'];
    $seller_id = $_POST['seller_id'];
    
    try {
        // Update product information
        $stmt = $db->prepare("UPDATE products SET name_en = ?,name_ar = ?, description_en = ?,description_ar=?, price = ?, seller_id = ? WHERE id = ?");
        $stmt->execute([$title,$title_ar, $description,$description_ar, $price, $seller_id, $product_id]);
        
        // Handle new image upload if provided
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
            $image_type = $_POST['image_type'];
            
            // Handle image upload
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            $targetFile = $targetDir . basename($_FILES["image"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            
            // Check if image file is actual image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "<div class='error'>File is not an image.</div>";
                $uploadOk = 0;
            }
            
            // Check if file already exists - if so, create unique name
            if (file_exists($targetFile)) {
                $filename = pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME);
                $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $counter = 1;
                do {
                    $newFilename = $filename . "_" . $counter . "." . $extension;
                    $targetFile = $targetDir . $newFilename;
                    $counter++;
                } while (file_exists($targetFile));
            }
            
            // Check file size (500KB limit)
            if ($_FILES["image"]["size"] > 500000) {
                echo "<div class='error'>Sorry, your file is too large.</div>";
                $uploadOk = 0;
            }
            
            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "<div class='error'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>";
                $uploadOk = 0;
            }
            
            // Check if upload is OK
            if ($uploadOk == 1) {
                // Try to upload the file
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    // Add new image to images table
                    $image_stmt = $db->prepare("INSERT INTO images (product_id, url, alt_text, seller_id, image_type) VALUES (?, ?, ?, ?, ?)");
                    $alt_text = $title; // Use product title as alt text
                    $image_stmt->execute([$product_id, $targetFile, $alt_text, $seller_id, $image_type]);
                    
                    echo "<div class='success'>Product updated and new image uploaded successfully!</div>";
                } else {
                    echo "<div class='error'>Sorry, there was an error uploading your file.</div>";
                }
            }
        } else {
            echo "<div class='success'>Product updated successfully!</div>";
        }
        
        // Clear edit mode after successful update
        $edit_product = null;
        
    } catch (PDOException $e) {
        echo "<div class='error'>Error updating product: " . $e->getMessage() . "</div>";
    }
}

// Handle upload request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload'])) {
    $title = $_POST['title'];
    $title_ar = $_POST['title_ar'];
    $description = $_POST['description'];
    $description_ar = $_POST['description_ar'];
    $price = $_POST['price'];
    $seller_id = $_POST['seller_id'];
    $image_type = $_POST['image_type'];
    
    // Handle image upload
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if image file is actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<div class='error'>File is not an image.</div>";
        $uploadOk = 0;
    }
    
    // Check if file already exists - if so, create unique name
    if (file_exists($targetFile)) {
        $filename = pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME);
        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $counter = 1;
        do {
            $newFilename = $filename . "_" . $counter . "." . $extension;
            $targetFile = $targetDir . $newFilename;
            $counter++;
        } while (file_exists($targetFile));
    }
    
    // Check file size (500KB limit)
    if ($_FILES["image"]["size"] > 500000) {
        echo "<div class='error'>Sorry, your file is too large.</div>";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "<div class='error'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>";
        $uploadOk = 0;
    }
    
    // Check if upload is OK
    if ($uploadOk == 0) {
        echo "<div class='error'>Sorry, your file was not uploaded.</div>";
    } else {
        // Try to upload the file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            echo "<div class='success'>The file " . htmlspecialchars(basename($targetFile)) . " has been uploaded.</div>";
            
            try {
                // First, insert product into products table
                $stmt = $db->prepare("INSERT INTO products (name_en,name_ar, description_en,description_ar, price, seller_id) VALUES (?, ?, ?,?,?,?)");
                $stmt->execute([$title,$title_ar, $description,$description_ar, $price, $seller_id]);
                
                $product_id = $db->lastInsertId(); // Get the ID of the newly inserted product
                
                // Then, insert image into images table
                $image_stmt = $db->prepare("INSERT INTO images (product_id, url, alt_text, seller_id, image_type) VALUES (?, ?, ?, ?, ?)");
                $alt_text = $title; // Use product title as alt text
                $image_stmt->execute([$product_id, $targetFile, $alt_text, $seller_id, $image_type]);
                
                echo "<div class='success'>New product added successfully!</div>";
                
            } catch (PDOException $e) {
                echo "<div class='error'>Error saving product: " . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div class='error'>Sorry, there was an error uploading your file.</div>";
        }
    }
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $image_id = intval($_GET['delete_image']);
    
    try {
        // Get image path before deleting
        $stmt = $db->prepare("SELECT url FROM images WHERE id = ?");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            // Delete image file from server
            if (file_exists($image['url'])) {
                unlink($image['url']);
            }
            
            // Delete image from database
            $delete_stmt = $db->prepare("DELETE FROM images WHERE id = ?");
            $delete_stmt->execute([$image_id]);
            
            echo "<div class='success'>Image deleted successfully!</div>";
        }
        
    } catch (PDOException $e) {
        echo "<div class='error'>Error deleting image: " . $e->getMessage() . "</div>";
    }
}

// Fetch all products with their images for display
try {
    $stmt = $db->prepare("SELECT p.*, i.id as image_id, i.url, i.alt_text, i.image_type 
                         FROM products p 
                         LEFT JOIN images i ON p.id = i.product_id 
                         ORDER BY p.id DESC");
    $stmt->execute();
    $products_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group products by ID to handle multiple images per product
    $products = [];
    foreach ($products_result as $row) {
        $product_id = $row['id'];
        if (!isset($products[$product_id])) {
            $products[$product_id] = [
                'id' => $row['id'],
                'name_en' => $row['name_en'],
                'description_en' => $row['description_en'],
                'price' => $row['price'],
                'seller_id' => $row['seller_id'],
                'images' => []
            ];
        }
        if (!empty($row['url'])) {
            $products[$product_id]['images'][] = [
                'id' => $row['image_id'],
                'url' => $row['url'],
                'alt_text' => $row['alt_text'],
                'image_type' => $row['image_type']
            ];
        }
    }
} catch (PDOException $e) {
    echo "<div class='error'>Error fetching products: " . $e->getMessage() . "</div>";
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        h1, h2 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"], input[type="number"], textarea, input[type="file"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        textarea {
            height: 100px;
            resize: vertical;
        }
        
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        
        .btn:hover {
            background-color: #45a049;
        }
        
        .btn-edit {
            background-color: #2196F3;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
            display: inline-block;
            margin-right: 10px;
        }
        
        .btn-edit:hover {
            background-color: #1976D2;
        }
        
        .btn-delete {
            background-color: #f44336;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
            display: inline-block;
            margin-right: 10px;
        }
        
        .btn-delete:hover {
            background-color: #da190b;
        }
        
        .btn-cancel {
            background-color: #757575;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
            display: inline-block;
        }
        
        .btn-cancel:hover {
            background-color: #616161;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .product-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .product-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .product-price {
            font-size: 20px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 15px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .no-products {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
        
        .image-gallery {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        
        .image-gallery img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            transition: opacity 0.3s;
        }
        
        .image-gallery img:not(:first-child) {
            opacity: 0;
        }
        
        .image-counter {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        
        .product-actions {
            margin-top: 15px;
            text-align: center;
        }
        
        .edit-form {
            border: 2px solid #2196F3;
            background-color: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .edit-form h3 {
            color: #2196F3;
            margin-top: 0;
        }
        
        .image-management {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .image-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }
        
        .image-item {
            text-align: center;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .image-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 3px;
        }
        
        .image-item small {
            display: block;
            margin: 5px 0;
            color: #666;
        }
        
        .image-item .btn-delete {
            padding: 4px 8px;
            font-size: 12px;
            margin: 0;
        }
    </style>
</head>
<body>
    <?php include("proHeader.html") ?>
    <div class="container">
        <h1>Product Management System</h1>
        
        <?php if ($edit_product): ?>
            <div class="edit-form">
                <h3>Edit Product</h3>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                    
                    <div class="form-group">
                        <label for="title">Product Title:</label>
                        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($edit_product['name_en']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="title_ar">Product Title in Arabic:</label>
                        <input type="text" name="title_ar" id="title_ar" value="<?php echo htmlspecialchars($edit_product['name_ar']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Product Description:</label>
                        <textarea name="description" id="description" required><?php echo htmlspecialchars($edit_product['description_en']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description_ar">Product Description in Arabic:</label>
                        <textarea name="description_ar" id="description_ar" required><?php echo htmlspecialchars($edit_product['description_ar']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price ($):</label>
                        <input type="number" name="price" id="price" step="0.01" min="0" value="<?php echo $edit_product['price']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="seller_id">Seller ID:</label>
                        <input type="number" name="seller_id" id="seller_id" value="<?php echo $edit_product['seller_id']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Add New Image (optional):</label>
                        <input type="file" name="image" id="image" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label for="image_type">Image Type:</label>
                        <select name="image_type" id="image_type">
                            <option value="product">Product Image</option>
                            <option value="thumbnail">Thumbnail</option>
                            <option value="gallery">Gallery Image</option>
                        </select>
                    </div>
                    
                    <input type="submit" name="update" value="Update Product" class="btn">
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn-cancel">Cancel</a>
                </form>
                
                <?php
                // Show existing images for this product
                $current_product_images = [];
                if (isset($products[$edit_product['id']])) {
                    $current_product_images = $products[$edit_product['id']]['images'];
                }
                
                if (!empty($current_product_images)): ?>
                    <div class="image-management">
                        <h4>Current Images</h4>
                        <div class="image-list">
                            <?php foreach($current_product_images as $image): ?>
                                <div class="image-item">
                                    <img src="<?php echo htmlspecialchars($image['url']); ?>" alt="<?php echo htmlspecialchars($image['alt_text']); ?>">
                                    <small><?php echo htmlspecialchars($image['image_type']); ?></small>
                                    <a href="?delete_image=<?php echo $image['id']; ?>&edit=<?php echo $edit_product['id']; ?>" 
                                       class="btn-delete" 
                                       onclick="return confirm('Are you sure you want to delete this image?')">
                                       Delete
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <h2>Add New Product</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="image">Product Image:</label>
                    <input type="file" name="image" id="image" accept="image/*" required>
                </div>
                
                <div class="form-group">
                    <label for="title">Product Title:</label>
                    <input type="text" name="title" id="title" required>
                </div>

                <div class="form-group">
                    <label for="title_ar">Product Title In Arabic:</label>
                    <input type="text" name="title_ar" id="title_ar" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Product Description:</label>
                    <textarea name="description" id="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="description_ar">Product Description in Arabic:</label>
                    <textarea name="description_ar" id="description_ar" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="seller_id">Seller ID:</label>
                    <input type="number" name="seller_id" id="seller_id" value="1" required>
                </div>
                
                <div class="form-group">
                    <label for="image_type">Image Type:</label>
                    <select name="image_type" id="image_type" required>
                        <option value="product">Product Image</option>
                        <option value="thumbnail">Thumbnail</option>
                        <option value="gallery">Gallery Image</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Price ($):</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" required>
                </div>
                
                <input type="submit" name="upload" value="Add Product" class="btn">
            </form>
        <?php endif; ?>
    </div>
    
    <div class="container">
        <h2>Manage Products</h2>
        
        <?php if (!empty($products)): ?>
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <?php if (!empty($product['images'])): ?>
                            <div class="image-gallery">
                                <?php foreach($product['images'] as $index => $image): ?>
                                    <?php if (file_exists($image['url'])): ?>
                                        <img src="<?php echo htmlspecialchars($image['url']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name_en']); ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if (count($product['images']) > 1): ?>
                                    <div class="image-counter"><?php echo count($product['images']); ?> images</div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div style="height: 200px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #666;">
                                No Image Available
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-info">
                            <div class="product-title"><?php echo htmlspecialchars($product['name_en']); ?></div>
                            <div class="product-description"><?php echo htmlspecialchars($product['description_en']); ?></div>
                            <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                            
                            <div class="product-actions">
                                <a href="?edit=<?php echo $product['id']; ?>" class="btn-edit">Edit Product</a>
                                <a href="?delete=<?php echo $product['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this product and all its images? This action cannot be undone.')">
                                   Delete Product
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-products">
                No products found. Add your first product above!
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Add image gallery functionality
        document.addEventListener('DOMContentLoaded', function() {
            const galleries = document.querySelectorAll('.image-gallery');
            
            galleries.forEach(gallery => {
                const images = gallery.querySelectorAll('img');
                if (images.length > 1) {
                    let currentIndex = 0;
                    
                    // Click to change image
                    gallery.addEventListener('click', function() {
                        images[currentIndex].style.opacity = '0';
                        currentIndex = (currentIndex + 1) % images.length;
                        images[currentIndex].style.opacity = '1';
                    });
                    
                    // Auto-change image every 4 seconds
                    setInterval(() => {
                        images[currentIndex].style.opacity = '0';
                        currentIndex = (currentIndex + 1) % images.length;
                        images[currentIndex].style.opacity = '1';
                    }, 4000);
                }
            });
        });
    </script>
</body>
</html>