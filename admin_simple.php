<?php
session_start();

// Configuration DB
$host = 'localhost';
$dbname = 'u634930929_Ino';
$username = 'u634930929_Ino';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur DB: " . $e->getMessage());
}

// Vérifier si connecté en tant qu'admin
$isAdmin = false;
if (isset($_SESSION['user_id'])) {
    $user = $pdo->query("SELECT role FROM users WHERE id = " . intval($_SESSION['user_id']))->fetch();
    $isAdmin = ($user && in_array($user['role'], ['admin', 'superadmin']));
}

// Traitement du formulaire de connexion
if ($_POST && !$isAdmin) {
    $login = trim($_POST['login'] ?? '');
    $pass = $_POST['password'] ?? '';
    
    if ($login && $pass) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND role IN ('admin', 'superadmin')");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $isAdmin = true;
        } else {
            $error = 'Identifiants admin incorrects';
        }
    }
}

// Traitement des paramètres
if ($_POST && $isAdmin && isset($_POST['save_settings'])) {
    try {
        $registration_enabled = isset($_POST['registration_enabled']) ? '1' : '0';
        
        // Mettre à jour ou insérer le paramètre
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('registration_enabled', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$registration_enabled, $registration_enabled]);
        
        $success = 'Paramètres sauvegardés avec succès !';
    } catch (Exception $e) {
        $error = 'Erreur: ' . $e->getMessage();
    }
}

// Récupérer les paramètres actuels
$settings = [];
try {
    $result = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $result->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Ignorer si la table n'existe pas
}

$registrationEnabled = ($settings['registration_enabled'] ?? '1') === '1';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Simple - MaickelSMM</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; background: #1a1a2e; color: white; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: rgba(255,255,255,0.1); padding: 30px; border-radius: 10px; margin: 20px 0; }
        input, button { padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        input[type="text"], input[type="password"] { width: 100%; }
        button { background: #00d4ff; color: white; border: none; cursor: pointer; }
        .error { color: #ff6b6b; margin: 10px 0; }
        .success { color: #00ff88; margin: 10px 0; }
        h1, h2 { color: #00d4ff; text-align: center; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; margin: 20px 0; }
        .info { background: rgba(0,212,255,0.2); padding: 15px; border-radius: 5px; margin: 20px 0; }
        a { color: #ff6b6b; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Admin Simple - MaickelSMM</h1>
        
        <?php if (!$isAdmin): ?>
            <!-- Formulaire de connexion admin -->
            <div class="card">
                <h2>Connexion Administrateur</h2>
                
                <?php if (isset($error)): ?>
                    <div class="error">❌ <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
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
            <!-- Panel admin -->
            <div class="card">
                <h2>⚙️ Paramètres du Site</h2>
                
                <?php if (isset($success)): ?>
                    <div class="success">✅ <?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="error">❌ <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="checkbox-group">
                        <input type="checkbox" id="registration_enabled" name="registration_enabled" 
                               <?= $registrationEnabled ? 'checked' : '' ?>>
                        <label for="registration_enabled">
                            <strong>Autoriser les inscriptions</strong>
                        </label>
                    </div>
                    
                    <button type="submit" name="save_settings">Sauvegarder les paramètres</button>
                </form>
                
                <div class="info">
                    <strong>État actuel :</strong><br>
                    Inscriptions : <?= $registrationEnabled ? '✅ ACTIVÉES' : '❌ DÉSACTIVÉES' ?><br><br>
                    
                    <strong>Test :</strong><br>
                    <a href="register.php" target="_blank">Tester la page d'inscription</a>
                </div>
            </div>
            
            <div class="card">
                <h2>🚀 Actions Rapides</h2>
                <p><a href="admin/index.php">📊 Panel admin complet</a></p>
                <p><a href="index.php">🏠 Voir le site</a></p>
                <p><a href="?logout=1">🚪 Se déconnecter</a></p>
            </div>
        <?php endif; ?>
        
        <p style="text-align: center; margin-top: 30px;">
            <a href="index.php">← Retour à l'accueil</a>
        </p>
    </div>
    
    <?php
    // Déconnexion
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: admin_simple.php');
        exit;
    }
    ?>
</body>
</html>