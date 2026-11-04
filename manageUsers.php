<?php
session_start();

// Database configuration
// $host = 'localhost';
// $dbname = 'your_database_name';
// $username = 'your_db_username';
// $password = 'your_db_password';

try {
    // $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    include("config.php");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $db->prepare("INSERT INTO users (email, password, first_name, last_name, role, active) VALUES (?, ?, ?, ?, ?, ?)");
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->execute([$_POST['email'], $hashedPassword, $_POST['first_name'], $_POST['last_name'], $_POST['role'], $_POST['active']]);
                $message = "User added successfully!";
                break;
                
            case 'edit':
                if (!empty($_POST['password'])) {
                    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET email = ?, password = ?, first_name = ?, last_name = ?, role = ?, active = ? WHERE id = ?");
                    $stmt->execute([$_POST['email'], $hashedPassword, $_POST['first_name'], $_POST['last_name'], $_POST['role'], $_POST['active'], $_POST['id']]);
                } else {
                    $stmt = $db->prepare("UPDATE users SET email = ?, first_name = ?, last_name = ?, role = ?, active = ? WHERE id = ?");
                    $stmt->execute([$_POST['email'], $_POST['first_name'], $_POST['last_name'], $_POST['role'], $_POST['active'], $_POST['id']]);
                }
                $message = "User updated successfully!";
                break;
                
            case 'delete':
                $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $message = "User deleted successfully!";
                break;
        }
    }
}

// Get user data for editing
$editUser = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all users
$stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: black;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
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
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        
        .role-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .role-admin {
            background-color: #dc3545;
            color: white;
        }
        
        .role-moderator {
            background-color: #ffc107;
            color: black;
        }
        
        .role-user {
            background-color: #28a745;
            color: white;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .user-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .logout-btn {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <?php include("proHeader.html") ?>
    <div class="container">
        <div class="header">
            <h1>User Management</h1>
            <div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</span>
                <a href="logout.php" class="btn logout-btn">Logout</a>
            </div>
        </div>
        
        <?php if (isset($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <!-- Add/Edit User Form -->
        <div class="user-form">
            <h2><?php echo $editUser ? 'Edit User' : 'Add New User'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $editUser ? 'edit' : 'add'; ?>">
                <?php if ($editUser): ?>
                    <input type="hidden" name="id" value="<?php echo $editUser['id']; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo $editUser ? htmlspecialchars($editUser['email']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" 
                               <?php echo $editUser ? '' : 'required'; ?>
                               placeholder="<?php echo $editUser ? 'Leave empty to keep current password' : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?php echo $editUser ? htmlspecialchars($editUser['first_name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?php echo $editUser ? htmlspecialchars($editUser['last_name']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select id="role" name="role" required>
                            <option value="user" <?php echo ($editUser && $editUser['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                            <option value="moderator" <?php echo ($editUser && $editUser['role'] === 'moderator') ? 'selected' : ''; ?>>Moderator</option>
                            <option value="admin" <?php echo ($editUser && $editUser['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="active">Status:</label>
                        <select id="active" name="active" required>
                            <option value="1" <?php echo ($editUser && $editUser['active'] == 1) ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo ($editUser && $editUser['active'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="actions">
                    <button type="submit" class="btn btn-success">
                        <?php echo $editUser ? 'Update User' : 'Add User'; ?>
                    </button>
                    <?php if ($editUser): ?>
                        <a href="manageUsers.php" class="btn btn-primary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Users Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    <td>
                        <span class="role-badge role-<?php echo $user['role']; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td class="<?php echo $user['active'] ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo $user['active'] ? 'Active' : 'Inactive'; ?>
                    </td>
                    <td><?php echo $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never'; ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                    <td class="actions">
                        <a href="?edit=<?php echo $user['id']; ?>" class="btn btn-warning">Edit</a>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>