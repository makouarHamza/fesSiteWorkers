<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// تحقق من تسجيل الدخول وتحقق أن الدور هو "admin"
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // إعادة التوجيه إلى صفحة تسجيل الدخول أو صفحة غير مصرح بها
    header("Location: login.php");
    exit();
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #0d47a1;
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            color: #1565c0;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #90caf9;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        input[type="file"] {
            margin-bottom: 15px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button[type="submit"] {
            width: 100%;
            background-color: #1976d2;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0d47a1;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #1976d2;
            box-shadow: 0 0 5px rgba(25, 118, 210, 0.5);
        }
    </style>

</head>
<body>
    <h2>Add New Product</h2>
    <form action="insert_product.php" method="post" enctype="multipart/form-data">
        <label>Product Name (EN):</label><br>
        <input type="text" name="name_en" required><br><br>

        <label>Product Name (AR):</label><br>
        <input type="text" name="name_ar" required><br><br>

        <label>Description (EN):</label><br>
        <textarea name="description_en" required></textarea><br><br>

        <label>Description (AR):</label><br>
        <textarea name="description_ar" required></textarea><br><br>

        <label>Price:</label><br>
        <input type="number" step="1" name="price" required><br><br>

        <label>Seller ID:</label><br>
        <input type="number" name="seller_id" required><br><br>

        <label>Status:</label><br>
        <select name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select><br><br>

        <label>Upload Images:</label><br>
        <input type="file" name="images[]" multiple accept="image/*"><br><br>

        <label>Alt Text for Images (comma-separated):</label><br>
        <input type="text" name="alt_texts" placeholder="e.g. alt1,alt2,..."><br><br>

        <label>Image Types (comma-separated):</label><br>
        <input type="text" name="image_types" placeholder="e.g. main,thumb,..."><br><br>

        <button type="submit">Add Product</button>
    </form>
</body>
</html>
