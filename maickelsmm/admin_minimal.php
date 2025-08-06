<?php
// Admin ultra-minimal - AUCUNE dépendance
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration DB
$DB_HOST = 'localhost';
$DB_NAME = 'u634930929_Ino';
$DB_USER = 'u634930929_Ino';
$DB_PASS = '';

try {
    $conn = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

// Session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier admin
$isAdmin = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $isAdmin = ($user && in_array($user['role'], ['admin', 'superadmin']));
}

// Formulaire de connexion admin
if ($_POST && !$isAdmin) {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($login && $password) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND role IN ('admin', 'superadmin')");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $isAdmin = true;
            $success = 'Connexion admin réussie !';
        } else {
            $error = 'Identifiants admin incorrects';
        }
    }
}

// Traitement paramètres
if ($_POST && $isAdmin && isset($_POST['save_settings'])) {
    try {
        $registration_enabled = isset($_POST['registration_enabled']) ? '1' : '0';
        
        // Créer ou mettre à jour
        $stmt = $conn->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = 'registration_enabled'");
        $stmt->execute();
        $exists = $stmt->fetchColumn();
        
        if ($exists) {
            $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'registration_enabled'");
        } else {
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('registration_enabled', ?)");
        }
        $stmt->execute([$registration_enabled]);
        
        $success = 'Paramètres sauvegardés !';
    } catch (Exception $e) {
        $error = 'Erreur: ' . $e->getMessage();
    }
}

// Récupérer paramètres
$registrationEnabled = true; // Par défaut
try {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = 'registration_enabled'");
    $stmt->execute();
    $setting = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($setting) {
        $registrationEnabled = $setting['setting_value'] === '1';
    }
} catch (Exception $e) {
    // Ignorer si pas de table settings
}

// Statistiques simples
$stats = ['orders' => 0, 'users' => 0, 'services' => 0];
if ($isAdmin) {
    try {
        $stats['orders'] = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $stats['users'] = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['services'] = $conn->query("SELECT COUNT(*) FROM services")->fetchColumn();
    } catch (Exception $e) {
        // Ignorer les erreurs
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Minimal - MaickelSMM</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #00d4ff;
            text-align: center;
        }
        .card {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
        }
        input, button {
            padding: 15px;
            margin: 10px 0;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            font-size: 16px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            background: rgba(255,255,255,0.1);
            color: white;
        }
        button {
            background: linear-gradient(45deg, #00d4ff, #ff6b6b);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
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
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #00d4ff;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }
        .links {
            text-align: center;
            margin: 20px 0;
        }
        .links a {
            color: #00d4ff;
            text-decoration: none;
            margin: 0 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Admin MaickelSMM</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if (!$isAdmin): ?>
            <!-- Connexion Admin -->
            <div class="card">
                <h2>Connexion Administrateur</h2>
                <form method="POST">
                    <input type="text" name="login" placeholder="Email ou nom d'utilisateur admin" required>
                    <input type="password" name="password" placeholder="Mot de passe admin" required>
                    <button type="submit">Se connecter</button>
                </form>
                
                <div class="info">
                    <strong>Compte admin par défaut :</strong><br>
                    Email: admin@maickelsmm.com<br>
                    Mot de passe: password123
                </div>
            </div>
        <?php else: ?>
            <!-- Panel Admin -->
            <div class="card">
                <h2>📊 Statistiques</h2>
                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['orders'] ?></div>
                        <div>Commandes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['users'] ?></div>
                        <div>Utilisateurs</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['services'] ?></div>
                        <div>Services</div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h2>⚙️ Paramètres</h2>
                <form method="POST">
                    <div class="checkbox-group">
                        <input type="checkbox" id="registration_enabled" name="registration_enabled" 
                               <?= $registrationEnabled ? 'checked' : '' ?>>
                        <label for="registration_enabled">
                            <strong>Autoriser les inscriptions</strong>
                        </label>
                    </div>
                    
                    <button type="submit" name="save_settings">Sauvegarder</button>
                </form>
                
                <div class="info">
                    <strong>État actuel :</strong><br>
                    Inscriptions : <?= $registrationEnabled ? '✅ ACTIVÉES' : '❌ DÉSACTIVÉES' ?>
                </div>
            </div>
            
            <div class="links">
                <a href="index.php">🏠 Voir le site</a>
                <a href="login_minimal.php">🔐 Autre connexion</a>
                <a href="?logout=1">🚪 Déconnexion</a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php
    // Déconnexion
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: login_minimal.php');
        exit;
    }
    ?>
</body>
</html>