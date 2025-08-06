<?php
echo "<h1>🔧 Réparation Problème de Login</h1>";

// Configuration DB - MODIFIEZ CES VALEURS
$host = 'localhost';
$dbname = 'u634930929_Ino';
$username = 'u634930929_Ino';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Connexion DB réussie</p>";
} catch (Exception $e) {
    die("<p style='color: red;'>❌ Erreur DB: " . $e->getMessage() . "</p>");
}

echo "<h2>1. Vérification Utilisateur Admin</h2>";

// Vérifier si l'admin existe
try {
    $admin = $pdo->query("SELECT * FROM users WHERE email = 'admin@maickelsmm.com'")->fetch();
    
    if ($admin) {
        echo "<p style='color: green;'>✅ Utilisateur admin trouvé</p>";
        echo "<p><strong>Email:</strong> " . $admin['email'] . "</p>";
        echo "<p><strong>Username:</strong> " . $admin['username'] . "</p>";
        echo "<p><strong>Rôle:</strong> " . $admin['role'] . "</p>";
        echo "<p><strong>Statut:</strong> " . $admin['status'] . "</p>";
        
        // Test du mot de passe
        if (password_verify('password123', $admin['password'])) {
            echo "<p style='color: green;'>✅ Mot de passe correct</p>";
        } else {
            echo "<p style='color: red;'>❌ Mot de passe incorrect - Réparation nécessaire</p>";
            
            // Corriger le mot de passe
            $newHash = password_hash('password123', PASSWORD_DEFAULT);
            $pdo->query("UPDATE users SET password = '$newHash' WHERE email = 'admin@maickelsmm.com'");
            echo "<p style='color: green;'>✅ Mot de passe admin réparé</p>";
        }
        
        // Vérifier le statut
        if ($admin['status'] !== 'active') {
            $pdo->query("UPDATE users SET status = 'active' WHERE email = 'admin@maickelsmm.com'");
            echo "<p style='color: green;'>✅ Compte admin activé</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Utilisateur admin non trouvé - Création en cours...</p>";
        
        // Créer l'utilisateur admin
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $pdo->query("INSERT INTO users (username, email, password, first_name, last_name, role, status, email_verified, created_at) VALUES ('admin', 'admin@maickelsmm.com', '$hashedPassword', 'Admin', 'MaickelSMM', 'superadmin', 'active', 1, NOW())");
        echo "<p style='color: green;'>✅ Utilisateur admin créé</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}

echo "<h2>2. Test de Connexion</h2>";

// Test de connexion simple
session_start();
$testLogin = 'admin@maickelsmm.com';
$testPassword = 'password123';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND status = 'active'");
    $stmt->execute([$testLogin, $testLogin]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($testPassword, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['first_name'];
        
        echo "<p style='color: green;'>✅ Test de connexion réussi</p>";
        echo "<p><strong>Utilisateur connecté:</strong> " . $user['first_name'] . " " . $user['last_name'] . "</p>";
        
        // Redirection selon le rôle
        $redirectUrl = in_array($user['role'], ['admin', 'superadmin']) ? 'admin_simple.php' : 'dashboard_simple.php';
        echo "<p><strong>Redirection recommandée:</strong> <a href='$redirectUrl'>$redirectUrl</a></p>";
        
    } else {
        echo "<p style='color: red;'>❌ Test de connexion échoué</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur test connexion: " . $e->getMessage() . "</p>";
}

echo "<h2>3. Vérification des Inscriptions</h2>";

// Vérifier si les inscriptions sont activées
try {
    $setting = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'registration_enabled'")->fetch();
    
    if ($setting) {
        $isEnabled = $setting['setting_value'] === '1';
        echo "<p style='color: " . ($isEnabled ? 'green' : 'red') . ";'>" . ($isEnabled ? '✅' : '❌') . " Inscriptions: " . ($isEnabled ? 'ACTIVÉES' : 'DÉSACTIVÉES') . "</p>";
        
        if (!$isEnabled) {
            $pdo->query("UPDATE settings SET setting_value = '1' WHERE setting_key = 'registration_enabled'");
            echo "<p style='color: green;'>✅ Inscriptions activées automatiquement</p>";
        }
    } else {
        // Créer le paramètre
        $pdo->query("INSERT INTO settings (setting_key, setting_value) VALUES ('registration_enabled', '1')");
        echo "<p style='color: green;'>✅ Paramètre d'inscription créé et activé</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠️ Pas de table settings (normal si pas encore configuré)</p>";
}

echo "<hr>";
echo "<h2>🚀 Solutions</h2>";

echo "<h3>✅ Pages de Connexion Disponibles</h3>";
echo "<ul>";
echo "<li><a href='login_ultra_simple.php' target='_blank'><strong>login_ultra_simple.php</strong></a> - Version qui marche toujours</li>";
echo "<li><a href='login.php' target='_blank'>login.php</a> - Version originale</li>";
echo "<li><a href='admin_simple.php' target='_blank'><strong>admin_simple.php</strong></a> - Admin simplifié</li>";
echo "</ul>";

echo "<h3>📋 Identifiants de Connexion</h3>";
echo "<div style='background: rgba(0,212,255,0.1); padding: 15px; border-radius: 8px; border: 1px solid #00d4ff;'>";
echo "<p><strong>Email:</strong> admin@maickelsmm.com</p>";
echo "<p><strong>Mot de passe:</strong> password123</p>";
echo "<p><strong>Rôle:</strong> Superadmin</p>";
echo "</div>";

echo "<h3>🎯 Étapes Recommandées</h3>";
echo "<ol>";
echo "<li><strong>Testez</strong> <a href='login_ultra_simple.php'>login_ultra_simple.php</a></li>";
echo "<li><strong>Connectez-vous</strong> avec les identifiants ci-dessus</li>";
echo "<li><strong>Allez dans</strong> <a href='admin_simple.php'>admin_simple.php</a></li>";
echo "<li><strong>Activez les inscriptions</strong> si nécessaire</li>";
echo "</ol>";

// CSS pour styliser
echo "<style>";
echo "body { font-family: Arial, sans-serif; background: #1a1a2e; color: white; padding: 20px; max-width: 800px; margin: 0 auto; }";
echo "h1, h2, h3 { color: #00d4ff; }";
echo "a { color: #ff6b6b; text-decoration: none; }";
echo "a:hover { text-decoration: underline; }";
echo "ul, ol { margin: 10px 0; }";
echo "hr { border: 1px solid rgba(255,255,255,0.2); margin: 30px 0; }";
echo "</style>";
?>