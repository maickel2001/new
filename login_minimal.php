<?php
// Version ultra-minimale - AUCUNE dépendance
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration DB directe - MODIFIEZ CES VALEURS
$DB_HOST = 'localhost';
$DB_NAME = 'u634930929_Ino';
$DB_USER = 'u634930929_Ino';
$DB_PASS = 'VotreMotDePasse'; // ⚠️ REMPLACEZ par votre mot de passe DB

$error = '';
$success = '';

// Connexion DB ultra-simple
try {
    $conn = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

// Démarrer session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Si déjà connecté, rediriger
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'superadmin'])) {
        header('Location: admin_minimal.php');
    } else {
        header('Location: dashboard_minimal.php');
    }
    exit;
}

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
            $stmt->execute([$login]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'];
                
                $success = 'Connexion réussie !';
                
                // Redirection
                if (in_array($user['role'], ['admin', 'superadmin'])) {
                    echo '<script>setTimeout(() => window.location.href = "admin_minimal.php", 1500);</script>';
                } else {
                    echo '<script>setTimeout(() => window.location.href = "dashboard_minimal.php", 1500);</script>';
                }
            } else {
                $error = 'Email ou mot de passe incorrect';
            }
        } catch(PDOException $e) {
            $error = 'Erreur: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Minimale - MaickelSMM</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: rgba(255,255,255,0.1);
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            color: #00d4ff;
            margin-bottom: 30px;
        }
        input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 16px;
        }
        input::placeholder {
            color: rgba(255,255,255,0.7);
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #00d4ff, #ff6b6b);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            opacity: 0.9;
        }
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
        }
        .error {
            background: rgba(255,0,0,0.2);
            border: 1px solid #ff4444;
            color: #ff4444;
        }
        .success {
            background: rgba(0,255,0,0.2);
            border: 1px solid #44ff44;
            color: #44ff44;
        }
        .info {
            background: rgba(0,212,255,0.2);
            border: 1px solid #00d4ff;
            color: #00d4ff;
            margin-top: 20px;
            font-size: 14px;
        }
        .links {
            margin-top: 20px;
        }
        .links a {
            color: #00d4ff;
            text-decoration: none;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Connexion MaickelSMM</h1>
        
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?> Redirection...</div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="email" name="login" placeholder="Email" 
                   value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        
        <div class="info">
            <strong>👤 Admin par défaut :</strong><br>
            Email: admin@maickelsmm.com<br>
            Mot de passe: password123
        </div>
        
        <div class="links">
            <a href="index.php">🏠 Accueil</a>
            <a href="register_minimal.php">📝 S'inscrire</a>
        </div>
    </div>
</body>
</html>