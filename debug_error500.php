<?php
// Script de diagnostic pour erreur 500
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnostic Erreur 500</h1>";

// Test 1: PHP basique
echo "<h2>1. Test PHP Basique</h2>";
echo "✅ PHP fonctionne - Version: " . phpversion() . "<br>";

// Test 2: Extensions PHP
echo "<h2>2. Extensions PHP</h2>";
$extensions = ['pdo', 'pdo_mysql', 'session', 'json'];
foreach ($extensions as $ext) {
    echo (extension_loaded($ext) ? "✅" : "❌") . " $ext<br>";
}

// Test 3: Connexion DB
echo "<h2>3. Test Connexion Base de Données</h2>";
try {
    $host = 'localhost';
    $dbname = 'u634930929_Ino';
    $username = 'u634930929_Ino';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion DB réussie<br>";
    
    // Test table users
    $result = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $result->fetchColumn();
    echo "✅ Table users: $count utilisateurs<br>";
    
} catch (Exception $e) {
    echo "❌ Erreur DB: " . $e->getMessage() . "<br>";
}

// Test 4: Sessions
echo "<h2>4. Test Sessions</h2>";
try {
    session_start();
    $_SESSION['test'] = 'ok';
    echo "✅ Sessions fonctionnent<br>";
} catch (Exception $e) {
    echo "❌ Erreur sessions: " . $e->getMessage() . "<br>";
}

// Test 5: Fichiers includes
echo "<h2>5. Test Fichiers Includes</h2>";
$files = [
    'config/config.php',
    'config/database.php',
    'includes/functions.php',
    'includes/auth.php',
    'includes/security.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
        
        // Test inclusion
        try {
            $content = file_get_contents($file);
            if (strpos($content, '<?php') === 0) {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;✅ Format PHP correct<br>";
            } else {
                echo "&nbsp;&nbsp;&nbsp;&nbsp;❌ Format PHP incorrect<br>";
            }
        } catch (Exception $e) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;❌ Erreur lecture: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ $file manquant<br>";
    }
}

// Test 6: Test inclusion progressive
echo "<h2>6. Test Inclusion Progressive</h2>";
$includes = [
    'config/config.php' => 'Configuration générale',
    'config/database.php' => 'Base de données',
    'includes/functions.php' => 'Fonctions',
    'includes/auth.php' => 'Authentification',
    'includes/security.php' => 'Sécurité'
];

foreach ($includes as $file => $desc) {
    try {
        if (file_exists($file)) {
            require_once $file;
            echo "✅ $desc inclus<br>";
        } else {
            echo "❌ $desc - fichier manquant<br>";
        }
    } catch (Error $e) {
        echo "❌ $desc - Erreur fatale: " . $e->getMessage() . "<br>";
        break;
    } catch (Exception $e) {
        echo "❌ $desc - Exception: " . $e->getMessage() . "<br>";
        break;
    }
}

// Test 7: Test utilisateur admin
echo "<h2>7. Test Utilisateur Admin</h2>";
try {
    $admin = $pdo->query("SELECT * FROM users WHERE email = 'admin@maickelsmm.com'")->fetch();
    if ($admin) {
        echo "✅ Utilisateur admin trouvé<br>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;Email: " . $admin['email'] . "<br>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;Rôle: " . $admin['role'] . "<br>";
        
        if (password_verify('password123', $admin['password'])) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;✅ Mot de passe correct<br>";
        } else {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;❌ Mot de passe incorrect<br>";
        }
    } else {
        echo "❌ Utilisateur admin non trouvé<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur test admin: " . $e->getMessage() . "<br>";
}

// Solutions
echo "<hr>";
echo "<h2>🚀 Solutions</h2>";
echo "<p><strong>Pages de test disponibles :</strong></p>";
echo "<ul>";
echo "<li><a href='login_ultra_simple.php'>login_ultra_simple.php</a> - Version minimale</li>";
echo "<li><a href='simple_test.php'>simple_test.php</a> - Test basique</li>";
echo "<li><a href='test_admin.php'>test_admin.php</a> - Test admin spécifique</li>";
echo "</ul>";

echo "<p><strong>Si login_ultra_simple.php fonctionne :</strong></p>";
echo "<ul>";
echo "<li>Le problème vient des fichiers includes/</li>";
echo "<li>Utilisez cette version temporairement</li>";
echo "</ul>";

echo "<p><strong>Si rien ne fonctionne :</strong></p>";
echo "<ul>";
echo "<li>Problème de configuration serveur</li>";
echo "<li>Vérifiez les logs d'erreur PHP dans votre panneau Hostinger</li>";
echo "</ul>";

// CSS
echo "<style>";
echo "body { font-family: Arial, sans-serif; background: #1a1a2e; color: white; padding: 20px; }";
echo "h1, h2 { color: #00d4ff; }";
echo "a { color: #ff6b6b; }";
echo "ul { margin: 10px 0; }";
echo "</style>";
?>