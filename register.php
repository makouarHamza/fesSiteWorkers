<?php
session_start();

// Configuration de la base de données
// $host = 'localhost';
// $dbname = 'your_database_name';
// $username = 'your_db_username';
// $password = 'your_db_password';

try {
    // $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    include("config.php");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des données
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    // $role = $_POST['role'] ?? 'user';
    
    // Validation email
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide";
    }
    
    // Validation mot de passe
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre";
    }
    
    // Confirmation mot de passe
    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    // Validation nom et prénom
    if (empty($first_name)) {
        $errors[] = "Le prénom est requis";
    }
    if (empty($last_name)) {
        $errors[] = "Le nom est requis";
    }
    
    // Validation rôle
    // if (!in_array($role, ['admin', 'user', 'moderator'])) {
    //     $role = 'user';
    // }
    
    // Vérifier si l'email existe déjà
    if (empty($errors)) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé";
        }
    }
    
    // Insertion en base de données
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("
                INSERT INTO users (email, password, first_name, last_name, active) 
                VALUES (?, ?, ?, ?, 1)
            ");
            
            $stmt->execute([$email, $hashed_password, $first_name, $last_name]);
            $success = true;
            
            // Optionnel : connecter automatiquement l'utilisateur
            $_SESSION['user_id'] = $db->lastInsertId();
            $_SESSION['user_email'] = $email;
            // $_SESSION['user_role'] = $role;
            
        } catch(PDOException $e) {
            $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        .boddy {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display:grid;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        
        button:hover {
            opacity: 0.9;
        }
        
        .error {
            background: #fee;
            color: #c33;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid #c33;
        }
        
        .success {
            background: #efe;
            color: #363;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid #363;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="boddy">
    <div class="container">
        <h2>Inscription</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                <p>Inscription réussie ! Bienvenue !</p>
                <p><a href="store.php">Accéder au tableau de bord</a></p>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <input type="password" id="password" name="password" required>
                    <small style="color: #666; font-size: 0.8rem;">
                        Au moins 8 caractères avec majuscule, minuscule et chiffre
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <label for="first_name">Prénom *</label>
                    <input type="text" id="first_name" name="first_name" required 
                           value="<?php echo htmlspecialchars($first_name ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Nom *</label>
                    <input type="text" id="last_name" name="last_name" required 
                           value="<?php echo htmlspecialchars($last_name ?? ''); ?>">
                </div>
                <!--                 
                <div class="form-group">
                    <label for="role">Rôle</label>
                    <select id="role" name="role">
                        <option value="user" <?php echo ($role ?? 'user') === 'user' ? 'selected' : ''; ?>>Utilisateur</option>
                        <option value="moderator" <?php echo ($role ?? '') === 'moderator' ? 'selected' : ''; ?>>Modérateur</option>
                        <option value="admin" <?php echo ($role ?? '') === 'admin' ? 'selected' : ''; ?>>Administrateur</option>
                    </select>
                </div> -->
                
                <button type="submit">S'inscrire</button>
            </form>
        <?php endif; ?>
        
        <div class="login-link">
            <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
    </div>
</body>
</html>