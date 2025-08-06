<?php
echo "<h1>🔧 Test de la Page d'Inscription - MaickelSMM</h1>";

// Test 1: Vérification des fichiers requis
echo "<h2>1. Fichiers Requis</h2>";
$requiredFiles = [
    'config/config.php',
    'includes/functions.php',
    'includes/auth.php',
    'includes/security.php',
    'register.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file<br>";
    } else {
        echo "❌ $file (MANQUANT)<br>";
    }
}

// Test 2: Inclusion des fichiers
echo "<h2>2. Test d'Inclusion</h2>";
try {
    require_once 'config/config.php';
    echo "✅ config/config.php inclus<br>";
    
    require_once 'includes/functions.php';
    echo "✅ includes/functions.php inclus<br>";
    
    require_once 'includes/auth.php';
    echo "✅ includes/auth.php inclus<br>";
    
    require_once 'includes/security.php';
    echo "✅ includes/security.php inclus<br>";
} catch (Exception $e) {
    echo "❌ Erreur d'inclusion : " . $e->getMessage() . "<br>";
}

// Test 3: Vérification de la classe Auth
echo "<h2>3. Test de la Classe Auth</h2>";
try {
    if (class_exists('Auth')) {
        echo "✅ Classe Auth disponible<br>";
        
        $auth = new Auth();
        echo "✅ Instance Auth créée<br>";
        
        // Test des méthodes
        if (method_exists($auth, 'register')) {
            echo "✅ Méthode register() disponible<br>";
        } else {
            echo "❌ Méthode register() MANQUANTE<br>";
        }
        
        if (method_exists($auth, 'isLoggedIn')) {
            echo "✅ Méthode isLoggedIn() disponible<br>";
        } else {
            echo "❌ Méthode isLoggedIn() MANQUANTE<br>";
        }
        
    } else {
        echo "❌ Classe Auth NON DISPONIBLE<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur avec Auth : " . $e->getMessage() . "<br>";
}

// Test 4: Vérification de la classe Security
echo "<h2>4. Test de la Classe Security</h2>";
try {
    if (class_exists('Security')) {
        echo "✅ Classe Security disponible<br>";
        
        // Test CSRF Token
        $token = Security::generateCSRFToken();
        echo "✅ Token CSRF généré : " . substr($token, 0, 10) . "...<br>";
        
        $isValid = Security::verifyCSRFToken($token);
        echo ($isValid ? "✅" : "❌") . " Vérification CSRF : " . ($isValid ? "OK" : "ÉCHEC") . "<br>";
        
    } else {
        echo "❌ Classe Security NON DISPONIBLE<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur avec Security : " . $e->getMessage() . "<br>";
}

// Test 5: Vérification des paramètres de registration
echo "<h2>5. Paramètres d'Inscription</h2>";
try {
    $settings = getSettings();
    $registrationEnabled = ($settings['registration_enabled'] ?? '1') === '1';
    echo ($registrationEnabled ? "✅" : "❌") . " Inscription activée : " . ($registrationEnabled ? "OUI" : "NON") . "<br>";
    
    if (isset($settings['site_name'])) {
        echo "✅ Nom du site : " . $settings['site_name'] . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur avec les paramètres : " . $e->getMessage() . "<br>";
}

// Test 6: Test de connexion base de données pour les utilisateurs
echo "<h2>6. Test Table Users</h2>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    
    // Test structure table users
    $structure = $db->fetchAll("DESCRIBE users");
    echo "✅ Structure table 'users' :<br>";
    foreach ($structure as $column) {
        echo "&nbsp;&nbsp;- " . $column['Field'] . " (" . $column['Type'] . ")<br>";
    }
    
    // Test count
    $count = $db->fetchOne("SELECT COUNT(*) as count FROM users");
    echo "✅ Nombre d'utilisateurs : " . $count['count'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erreur base de données : " . $e->getMessage() . "<br>";
}

// Test 7: Test simple d'insertion
echo "<h2>7. Test d'Inscription Simulé</h2>";
try {
    // Données de test
    $testData = [
        'username' => 'test_user_' . time(),
        'email' => 'test' . time() . '@example.com',
        'password' => 'TestPassword123!',
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone' => '+1234567890'
    ];
    
    echo "📋 Données de test préparées<br>";
    echo "&nbsp;&nbsp;Username: " . $testData['username'] . "<br>";
    echo "&nbsp;&nbsp;Email: " . $testData['email'] . "<br>";
    
    // Test validation email
    if (filter_var($testData['email'], FILTER_VALIDATE_EMAIL)) {
        echo "✅ Format email valide<br>";
    } else {
        echo "❌ Format email invalide<br>";
    }
    
    // Test hash password
    $hashedPassword = password_hash($testData['password'], PASSWORD_DEFAULT);
    if ($hashedPassword) {
        echo "✅ Hash password généré<br>";
    } else {
        echo "❌ Erreur hash password<br>";
    }
    
    echo "<br><strong>⚠️ Test d'insertion NON EXÉCUTÉ pour éviter les données de test</strong><br>";
    
} catch (Exception $e) {
    echo "❌ Erreur test inscription : " . $e->getMessage() . "<br>";
}

// Test 8: Vérification du fichier register.php
echo "<h2>8. Analyse du Fichier register.php</h2>";
try {
    $registerContent = file_get_contents('register.php');
    
    // Vérifications basiques
    $checks = [
        'require_once' => strpos($registerContent, 'require_once') !== false,
        'POST method' => strpos($registerContent, '$_POST') !== false,
        'CSRF token' => strpos($registerContent, 'csrf_token') !== false,
        'Auth register' => strpos($registerContent, '$auth->register') !== false,
        'HTML form' => strpos($registerContent, '<form') !== false
    ];
    
    foreach ($checks as $check => $result) {
        echo ($result ? "✅" : "❌") . " $check : " . ($result ? "PRÉSENT" : "MANQUANT") . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur lecture register.php : " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>🚀 Recommandations</h2>";
echo "<p>1. Testez d'abord cette page : <a href='debug_register.php'>debug_register.php</a></p>";
echo "<p>2. Ensuite testez : <a href='register.php'>register.php</a></p>";
echo "<p>3. Vérifiez les erreurs dans les logs PHP de votre hébergeur</p>";
echo "<p>4. Si nécessaire, nous créerons une version simplifiée de register.php</p>";
?>