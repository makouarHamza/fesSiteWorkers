<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // إعادة التوجيه إلى صفحة تسجيل الدخول أو صفحة غير مصرح بها
    header("Location: login.php");
    exit();
}

$errors = [];
$success = false;
$deleteSuccess = false;
$editSuccess = false;
$sellers = [];

// Handle POST requests (Add/Delete/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Add seller
    if ($action === 'add') {
        $name    = trim($_POST['name']    ?? '');
        $contact = trim($_POST['contact'] ?? '');
        $souk    = trim($_POST['souk']    ?? '');

        if ($name === '')                  $errors[] = 'Name is required.';
        if ($contact === '')               $errors[] = 'Contact info is required.';
        if (!is_numeric($souk) || $souk<1) $errors[] = 'Souk ID must be a positive integer.';

        // Handle profile image upload
        $profileImagePath = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/sellersProfile/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileInfo = pathinfo($_FILES['profile_image']['name']);
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileExt = strtolower($fileInfo['extension']);
            
            if (!in_array($fileExt, $allowedTypes)) {
                $errors[] = 'Invalid image format. Allowed: JPG, JPEG, PNG, GIF, WEBP';
            } else if ($_FILES['profile_image']['size'] > 5 * 1024 * 1024) { // 5MB limit
                $errors[] = 'Image file is too large. Maximum size is 5MB.';
            } else {
                $fileName = uniqid('seller_') . '.' . $fileExt;
                $profileImagePath = $uploadDir . $fileName;
                
                if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $profileImagePath)) {
                    $errors[] = 'Failed to upload profile image.';
                    $profileImagePath = null;
                }
            }
        }

        if (!$errors) {
            include("config.php");

            if (!$db) {
                $errors[] = 'Database connection failed.';
            } else {
                $db->beginTransaction();
                
                try {
                    // Insert seller
                    $stmt = $db->prepare(
                        'INSERT INTO sellers (name, contact_info, souk_id) VALUES (?, ?, ?)'
                    );

                    if (!$stmt->execute([$name, $contact, $souk])) {
                        throw new Exception('Could not insert seller.');
                    }
                    
                    $sellerId = $db->lastInsertId();
                    
                    // Insert profile image if uploaded
                    if ($profileImagePath) {
                        $imageStmt = $db->prepare(
                            'INSERT INTO images (url, alt_text, seller_id, image_type) VALUES (?, ?, ?, ?)'
                        );
                        
                        if (!$imageStmt->execute([$profileImagePath, $name . ' profile photo', $sellerId, 'profile'])) {
                            throw new Exception('Could not save profile image information.');
                        }
                    }
                    
                    $db->commit();
                    $success = true;
                    
                } catch (Exception $e) {
                    $db->rollback();
                    $errors[] = 'Database error: ' . $e->getMessage();
                    
                    // Clean up uploaded file if database failed
                    if ($profileImagePath && file_exists($profileImagePath)) {
                        unlink($profileImagePath);
                    }
                }
            }
        }
    }
    
    // Edit seller
    if ($action === 'edit') {
        $sellerId = $_POST['seller_id'] ?? '';
        $name     = trim($_POST['name']    ?? '');
        $contact  = trim($_POST['contact'] ?? '');
        $souk     = trim($_POST['souk']    ?? '');

        if (!is_numeric($sellerId) || $sellerId < 1) $errors[] = 'Invalid seller ID.';
        if ($name === '')                            $errors[] = 'Name is required.';
        if ($contact === '')                         $errors[] = 'Contact info is required.';
        if (!is_numeric($souk) || $souk<1)          $errors[] = 'Souk ID must be a positive integer.';

        // Handle profile image upload
        $newProfileImagePath = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/sellersProfile/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileInfo = pathinfo($_FILES['profile_image']['name']);
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileExt = strtolower($fileInfo['extension']);
            
            if (!in_array($fileExt, $allowedTypes)) {
                $errors[] = 'Invalid image format. Allowed: JPG, JPEG, PNG, GIF, WEBP';
            } else if ($_FILES['profile_image']['size'] > 5 * 1024 * 1024) {
                $errors[] = 'Image file is too large. Maximum size is 5MB.';
            } else {
                $fileName = uniqid('seller_') . '.' . $fileExt;
                $newProfileImagePath = $uploadDir . $fileName;
                
                if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $newProfileImagePath)) {
                    $errors[] = 'Failed to upload profile image.';
                    $newProfileImagePath = null;
                }
            }
        }

        if (!$errors) {
            include("config.php");

            if (!$db) {
                $errors[] = 'Database connection failed.';
            } else {
                $db->beginTransaction();
                
                try {
                    // Check if seller exists first
                    $checkStmt = $db->prepare('SELECT id FROM sellers WHERE id = ?');
                    $checkStmt->execute([$sellerId]);
                    
                    if (!$checkStmt->fetch()) {
                        throw new Exception('Seller not found.');
                    }
                    
                    // Update seller
                    $stmt = $db->prepare(
                        'UPDATE sellers SET name = ?, contact_info = ?, souk_id = ? WHERE id = ?'
                    );

                    if (!$stmt->execute([$name, $contact, $souk, $sellerId])) {
                        throw new Exception('Failed to update seller information.');
                    }
                    
                    // Handle profile image update
                    if ($newProfileImagePath) {
                        // Get current profile image
                        $currentImageStmt = $db->prepare(
                            'SELECT url FROM images WHERE seller_id = ? AND image_type = ?'
                        );
                        $currentImageStmt->execute([$sellerId, 'profile']);
                        $currentImage = $currentImageStmt->fetch(PDO::FETCH_ASSOC);
                        
                        // Delete old image file
                        if ($currentImage && file_exists($currentImage['url'])) {
                            unlink($currentImage['url']);
                        }
                        
                        // Handle different database systems - try INSERT ... ON DUPLICATE KEY UPDATE first
                        $imageStmt = $db->prepare(
                            'SELECT COUNT(*) FROM images WHERE seller_id = ? AND image_type = ?'
                        );
                        $imageStmt->execute([$sellerId, 'profile']);
                        $imageExists = $imageStmt->fetchColumn() > 0;
                        
                        if ($imageExists) {
                            // Update existing image
                            $updateImageStmt = $db->prepare(
                                'UPDATE images SET url = ?, alt_text = ? WHERE seller_id = ? AND image_type = ?'
                            );
                            
                            if (!$updateImageStmt->execute([$newProfileImagePath, $name . ' profile photo', $sellerId, 'profile'])) {
                                throw new Exception('Could not update profile image information.');
                            }
                        } else {
                            // Insert new image
                            $insertImageStmt = $db->prepare(
                                'INSERT INTO images (url, alt_text, seller_id, image_type) VALUES (?, ?, ?, ?)'
                            );
                            
                            if (!$insertImageStmt->execute([$newProfileImagePath, $name . ' profile photo', $sellerId, 'profile'])) {
                                throw new Exception('Could not save profile image information.');
                            }
                        }
                    }
                    
                    $db->commit();
                    $editSuccess = true;
                    
                } catch (Exception $e) {
                    $db->rollback();
                    $errors[] = 'Database error: ' . $e->getMessage();
                    
                    // Clean up uploaded file if database failed
                    if ($newProfileImagePath && file_exists($newProfileImagePath)) {
                        unlink($newProfileImagePath);
                    }
                }
            }
        }
    }
    
    // Delete seller
    if ($action === 'delete') {
        $sellerId = $_POST['seller_id'] ?? '';
        
        if (!is_numeric($sellerId) || $sellerId < 1) {
            $errors[] = 'Invalid seller ID.';
        } else {
            include("config.php");
            
            if (!$db) {
                $errors[] = 'Database connection failed.';
            } else {
                $db->beginTransaction();
                
                try {
                    // Check if seller exists first
                    $checkStmt = $db->prepare('SELECT id FROM sellers WHERE id = ?');
                    $checkStmt->execute([$sellerId]);
                    
                    if (!$checkStmt->fetch()) {
                        throw new Exception('Seller not found.');
                    }
                    
                    // Get ALL images for this seller (not just profile) to delete files
                    $imageStmt = $db->prepare('SELECT url FROM images WHERE seller_id = ?');
                    $imageStmt->execute([$sellerId]);
                    $images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Delete image records FIRST (child table)
                    $deleteImageStmt = $db->prepare('DELETE FROM images WHERE seller_id = ?');
                    if (!$deleteImageStmt->execute([$sellerId])) {
                        throw new Exception('Failed to delete seller images.');
                    }
                    
                    // Now delete seller (parent table)
                    $stmt = $db->prepare('DELETE FROM sellers WHERE id = ?');
                    if (!$stmt->execute([$sellerId])) {
                        throw new Exception('Failed to delete seller.');
                    }
                    
                    $db->commit();
                    
                    // Delete image files after successful database deletion
                    foreach ($images as $image) {
                        if ($image['url'] && file_exists($image['url'])) {
                            unlink($image['url']);
                        }
                    }
                    
                    $deleteSuccess = true;
                    
                } catch (Exception $e) {
                    $db->rollback();
                    $errors[] = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Fetch all sellers with their profile images for display
try {
    include("config.php");
    if ($db) {
        $stmt = $db->prepare('
            SELECT s.id, s.name, s.contact_info, s.souk_id, i.url as profile_image
            FROM sellers s
            LEFT JOIN images i ON s.id = i.seller_id AND i.image_type = "profile"
            ORDER BY s.name
        ');
        $stmt->execute();
        $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $errors[] = 'Error fetching sellers: ' . $e->getMessage();
}

$_SESSION['csrf'] = bin2hex(random_bytes(32));
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Seller Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    .edit-form {
      display: none;
    }
    .edit-form.active {
      display: table-row;
    }
    .seller-row.editing {
      display: none;
    }
    .profile-image {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #dee2e6;
    }
    .profile-image-placeholder {
      width: 50px;
      height: 50px;
      background-color: #f8f9fa;
      border: 2px solid #dee2e6;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6c757d;
      font-size: 20px;
    }
    .image-upload-preview {
      max-width: 100px;
      max-height: 100px;
      object-fit: cover;
      border-radius: 8px;
      margin-top: 10px;
    }
  </style>
</head>
<body class="bg-light py-5">
  <div>
    <?php include("proHeader.html")?>
  </div>
   
<div class="container">
  
  <div class="row">
    <!-- Add Seller Form -->
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Seller</h4>
        </div>
        <div class="card-body">
          
          <?php if ($success): ?>
            <div class="alert alert-success">
              <i class="fas fa-check-circle me-2"></i>Seller added successfully!
            </div>
          <?php endif; ?>
          
          <?php if ($editSuccess): ?>
            <div class="alert alert-success">
              <i class="fas fa-edit me-2"></i>Seller updated successfully!
            </div>
          <?php endif; ?>
          
          <?php if ($errors && !$deleteSuccess): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
              </ul>
            </div>
          <?php endif; ?>
          
          <form method="post" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="add">
            
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" class="form-control" name="name" required
                     value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Contact info</label>
              <input type="text" class="form-control" name="contact" required
                     value="<?= htmlspecialchars($_POST['contact'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Souk ID</label>
              <input type="number" min="1" class="form-control" name="souk" required
                     value="<?= htmlspecialchars($_POST['souk'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Profile Image</label>
              <input type="file" class="form-control" name="profile_image" accept="image/*"
                     onchange="previewImage(this, 'add-preview')">
              <div class="form-text">Optional. Accepted formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 5MB.</div>
              <img id="add-preview" class="image-upload-preview" style="display: none;" />
            </div>
            
            <button class="btn btn-primary w-100">
              <i class="fas fa-plus me-2"></i>Add Seller
            </button>
          </form>
        </div>
      </div>
    </div>
    
    <!-- Sellers List -->
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-danger text-white">
          <h4 class="mb-0"><i class="fas fa-users me-2"></i>Manage Sellers</h4>
        </div>
        <div class="card-body">
          
          <?php if ($deleteSuccess): ?>
            <div class="alert alert-success">
              <i class="fas fa-trash-alt me-2"></i>Seller deleted successfully!
            </div>
          <?php endif; ?>
          
          <?php if ($errors && $deleteSuccess): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
              </ul>
            </div>
          <?php endif; ?>
          
          <?php if (empty($sellers)): ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>No sellers found.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    
                    <th>Profile</th>
                    <th>id</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Souk ID</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($sellers as $seller): ?>
                    <!-- Display Row -->
                    <tr class="seller-row" id="row-<?= $seller['id'] ?>">
                      
                      <td>
                        <?php if ($seller['profile_image'] && file_exists($seller['profile_image'])): ?>
                          <img src="<?= htmlspecialchars($seller['profile_image']) ?>" 
                               alt="<?= htmlspecialchars($seller['name']) ?>" 
                               class="profile-image">
                        <?php else: ?>
                          <div class="profile-image-placeholder">
                            <i class="fas fa-user"></i>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?= htmlspecialchars($seller['id']) ?>
                      </td>
                      <td><?= htmlspecialchars($seller['name']) ?></td>
                      <td><?= htmlspecialchars($seller['contact_info']) ?></td>
                      <td><?= htmlspecialchars($seller['souk_id']) ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                onclick="toggleEdit(<?= $seller['id'] ?>)">
                          <i class="fas fa-edit"></i>
                        </button>
                        <form method="post" style="display: inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this seller?')">
                          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                          <input type="hidden" name="action" value="delete">
                          <input type="hidden" name="seller_id" value="<?= $seller['id'] ?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash-alt"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                    
                    <!-- Edit Row -->
                    <tr class="edit-form" id="edit-<?= $seller['id'] ?>">
                      <td colspan="5">
                        <form method="post" enctype="multipart/form-data" class="row g-2">
                          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                          <input type="hidden" name="action" value="edit">
                          <input type="hidden" name="seller_id" value="<?= $seller['id'] ?>">
                          
                          <div class="col-md-3">
                            <label class="form-label small">Name</label>
                            <input type="text" class="form-control form-control-sm" name="name" 
                                   value="<?= htmlspecialchars($seller['name']) ?>" required>
                          </div>
                          
                          <div class="col-md-3">
                            <label class="form-label small">Contact</label>
                            <input type="text" class="form-control form-control-sm" name="contact" 
                                   value="<?= htmlspecialchars($seller['contact_info']) ?>" required>
                          </div>
                          
                          <div class="col-md-2">
                            <label class="form-label small">Souk ID</label>
                            <input type="number" min="1" class="form-control form-control-sm" name="souk" 
                                   value="<?= htmlspecialchars($seller['souk_id']) ?>" required>
                          </div>
                          
                          <div class="col-md-4">
                            <label class="form-label small">Profile Image</label>
                            <input type="file" class="form-control form-control-sm" name="profile_image" 
                                   accept="image/*" onchange="previewImage(this, 'edit-preview-<?= $seller['id'] ?>')">
                            <img id="edit-preview-<?= $seller['id'] ?>" class="image-upload-preview" style="display: none;" />
                          </div>
                          
                          <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-success me-1">
                              <i class="fas fa-save"></i> Save
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" 
                                    onclick="toggleEdit(<?= $seller['id'] ?>)">
                              <i class="fas fa-times"></i> Cancel
                            </button>
                          </div>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleEdit(sellerId) {
    const row = document.getElementById('row-' + sellerId);
    const editForm = document.getElementById('edit-' + sellerId);
    
    if (row.classList.contains('editing')) {
        // Cancel edit
        row.classList.remove('editing');
        editForm.classList.remove('active');
    } else {
        // Start edit
        row.classList.add('editing');
        editForm.classList.add('active');
    }
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
</body>
</html>