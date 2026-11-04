<?php
// Database configuration
// $host = 'localhost';
// $dbname = 'your_database_name';
// $username = 'your_username';
// $password = 'your_password';


try {
    // $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    include("config.php");
    // $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// تحقق من تسجيل الدخول وتحقق أن الدور هو "admin"
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // إعادة التوجيه إلى صفحة تسجيل الدخول أو صفحة غير مصرح بها
    header("Location: login.php");
    exit();
}


// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'create':
            createSouk($db);
            break;
        case 'update':
            updateSouk($db);
            break;
        case 'delete':
            deleteSouk($db);
            break;
    }
}

// Handle GET actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? 0;
    
    switch($action) {
        case 'delete':
            deleteSouk($db, $id);
            break;
        case 'edit':
            $editSouk = getSoukById($db, $id);
            break;
    }
}

// Functions
function createSouk($db) {
    try {
        $stmt = $db->prepare("INSERT INTO souks (name_en, name_ar, category, location, description_en, description_ar, city_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name_en'],
            $_POST['name_ar'],
            $_POST['category'],
            $_POST['location'],
            $_POST['description_en'],
            $_POST['description_ar'],
            $_POST['city_id']
        ]);
        $success = "Souk created successfully!";
    } catch(PDOException $e) {
        $error = "Error creating souk: " . $e->getMessage();
    }
    return $success ?? $error ?? '';
}

function updateSouk($db) {
    try {
        $stmt = $db->prepare("UPDATE souks SET name_en=?, name_ar=?, category=?, location=?, description_en=?, description_ar=?, city_id=? WHERE id=?");
        $stmt->execute([
            $_POST['name_en'],
            $_POST['name_ar'],
            $_POST['category'],
            $_POST['location'],
            $_POST['description_en'],
            $_POST['description_ar'],
            $_POST['city_id'],
            $_POST['id']
        ]);
        $success = "Souk updated successfully!";
    } catch(PDOException $e) {
        $error = "Error updating souk: " . $e->getMessage();
    }
    return $success ?? $error ?? '';
}

function deleteSouk($db, $id = null) {
    $id = $id ?? $_POST['id'];
    try {
        $stmt = $db->prepare("DELETE FROM souks WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Souk deleted successfully!";
    } catch(PDOException $e) {
        $error = "Error deleting souk: " . $e->getMessage();
    }
    return $success ?? $error ?? '';
}

function getSoukById($db, $id) {
    $stmt = $db->prepare("SELECT * FROM souks WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllSouks($db) {
    $stmt = $db->query("
        SELECT s.*, c.name_en as city_name_en, c.name_ar as city_name_ar 
        FROM souks s 
        LEFT JOIN cities c ON s.city_id = c.id 
        ORDER BY s.id DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllCities($db) {
    $stmt = $db->query("SELECT * FROM cities ORDER BY name_en");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get data for display
$souks = getAllSouks($db);
$cities = getAllCities($db);
$editSouk = $editSouk ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Souks Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .headerM {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .headerM h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .form-section {
            padding: 30px;
            border-bottom: 2px solid #eee;
        }

        .form-title {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 250px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-warning {
            background: #f39c12;
            color: white;
        }

        .btn-warning:hover {
            background: #e67e22;
        }

        .btn-cancel {
            background: #95a5a6;
            color: white;
            margin-left: 10px;
        }

        .btn-cancel:hover {
            background: #7f8c8d;
        }

        .table-section {
            padding: 30px;
        }

        .table-title {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .actions {
            white-space: nowrap;
        }

        .actions a {
            margin-right: 10px;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-weight: bold;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .category-badge {
            background: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .arabic-text {
            direction: rtl;
            font-family: 'Arial', sans-serif;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
            
            .form-group {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include("proHeader.html") ?>
     
    <div class="container">
        
        <div class="headerM">
            <h1>🏪 Souks Management System</h1>
            <p>Manage traditional markets and bazaars</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="form-section">
            <h2 class="form-title">
                <?php echo $editSouk ? 'Edit Souk' : 'Add New Souk'; ?>
            </h2>

            <form method="POST" action="">
                <input type="hidden" name="action" value="<?php echo $editSouk ? 'update' : 'create'; ?>">
                <?php if ($editSouk): ?>
                    <input type="hidden" name="id" value="<?php echo $editSouk['id']; ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name_en">Name (English) *</label>
                        <input type="text" id="name_en" name="name_en" required 
                               value="<?php echo htmlspecialchars($editSouk['name_en'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="name_ar">Name (Arabic) *</label>
                        <input type="text" id="name_ar" name="name_ar" required 
                               value="<?php echo htmlspecialchars($editSouk['name_ar'] ?? ''); ?>" 
                               class="arabic-text">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Traditional Market" <?php echo ($editSouk['category'] ?? '') === 'Traditional Market' ? 'selected' : ''; ?>>Traditional Market</option>
                            <option value="Handicrafts" <?php echo ($editSouk['category'] ?? '') === 'Handicrafts' ? 'selected' : ''; ?>>Handicrafts</option>
                            <option value="Spices & Food" <?php echo ($editSouk['category'] ?? '') === 'Spices & Food' ? 'selected' : ''; ?>>Spices & Food</option>
                            <option value="Textiles" <?php echo ($editSouk['category'] ?? '') === 'Textiles' ? 'selected' : ''; ?>>Textiles</option>
                            <option value="Jewelry" <?php echo ($editSouk['category'] ?? '') === 'Jewelry' ? 'selected' : ''; ?>>Jewelry</option>
                            <option value="Antiques" <?php echo ($editSouk['category'] ?? '') === 'Antiques' ? 'selected' : ''; ?>>Antiques</option>
                            <option value="Mixed" <?php echo ($editSouk['category'] ?? '') === 'Mixed' ? 'selected' : ''; ?>>Mixed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city_id">City *</label>
                        <select id="city_id" name="city_id" required>
                            <option value="">Select City</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo $city['id']; ?>" 
                                        <?php echo ($editSouk['city_id'] ?? '') == $city['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($city['name_en']); ?> - <?php echo htmlspecialchars($city['name_ar']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" id="location" name="location" required 
                               value="<?php echo htmlspecialchars($editSouk['location'] ?? ''); ?>"
                               placeholder="e.g., Old Medina, Downtown, etc.">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="description_en">Description (English)</label>
                        <textarea id="description_en" name="description_en" rows="4"><?php echo htmlspecialchars($editSouk['description_en'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="description_ar">Description (Arabic)</label>
                        <textarea id="description_ar" name="description_ar" rows="4" 
                                  class="arabic-text"><?php echo htmlspecialchars($editSouk['description_ar'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $editSouk ? '✏️ Update Souk' : '➕ Add Souk'; ?>
                        </button>
                        <?php if ($editSouk): ?>
                            <a href="?" class="btn btn-cancel">❌ Cancel</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-section">
            <h2 class="table-title">📋 All Souks</h2>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name (EN)</th>
                            <th>Name (AR)</th>
                            <th>Category</th>
                            <th>City</th>
                            <th>Location</th>
                            <th>Description (EN)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($souks)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 30px; color: #888;">
                                    No souks found. Add your first souk above! 🏪
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($souks as $souk): ?>
                                <tr>
                                    <td><?php echo $souk['id']; ?></td>
                                    <td><?php echo htmlspecialchars($souk['name_en']); ?></td>
                                    <td class="arabic-text"><?php echo htmlspecialchars($souk['name_ar']); ?></td>
                                    <td>
                                        <span class="category-badge">
                                            <?php echo htmlspecialchars($souk['category']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($souk['city_name_en'] ?? 'N/A'); ?>
                                        <?php if ($souk['city_name_ar']): ?>
                                            <br><span class="arabic-text"><?php echo htmlspecialchars($souk['city_name_ar']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($souk['location']); ?></td>
                                    <td>
                                        <?php 
                                        $desc = $souk['description_en'];
                                        echo strlen($desc) > 100 ? substr(htmlspecialchars($desc), 0, 100) . '...' : htmlspecialchars($desc);
                                        ?>
                                    </td>
                                    <td class="actions">
                                        <a href="?action=edit&id=<?php echo $souk['id']; ?>" class="btn btn-warning">
                                            ✏️ Edit
                                        </a>
                                        <a href="?action=delete&id=<?php echo $souk['id']; ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this souk?')">
                                            🗑️ Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus on first input
            const firstInput = document.querySelector('input[type="text"]');
            if (firstInput) {
                firstInput.focus();
            }

            // Add confirmation for delete actions
            const deleteLinks = document.querySelectorAll('a[href*="action=delete"]');
            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this souk? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            });

            // Form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let hasError = false;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.style.borderColor = '#e74c3c';
                            hasError = true;
                        } else {
                            field.style.borderColor = '#ddd';
                        }
                    });

                    if (hasError) {
                        e.preventDefault();
                        alert('Please fill in all required fields.');
                    }
                });
            }
        });
    </script>
</body>
</html>