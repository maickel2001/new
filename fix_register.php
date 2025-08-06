<?php
echo "<h1>🔧 Diagnostic et Réparation Register.php</h1>";

// Test 1: Vérifier les fichiers requis
echo "<h2>1. Vérification des Dépendances</h2>";
$files = [
    'config/config.php' => file_exists('config/config.php'),
    'includes/functions.php' => file_exists('includes/functions.php'),
    'includes/auth.php' => file_exists('includes/auth.php'),
    'includes/security.php' => file_exists('includes/security.php')
];

foreach ($files as $file => $exists) {
    echo ($exists ? "✅" : "❌") . " $file<br>";
}

// Test 2: Inclure les fichiers un par un
echo "<h2>2. Test d'Inclusion Séquentiel</h2>";
try {
    require_once 'config/config.php';
    echo "✅ config/config.php - OK<br>";
} catch (Exception $e) {
    echo "❌ config/config.php - ERREUR: " . $e->getMessage() . "<br>";
    exit;
}

try {
    require_once 'includes/functions.php';
    echo "✅ includes/functions.php - OK<br>";
} catch (Exception $e) {
    echo "❌ includes/functions.php - ERREUR: " . $e->getMessage() . "<br>";
}

try {
    require_once 'includes/auth.php';
    echo "✅ includes/auth.php - OK<br>";
} catch (Exception $e) {
    echo "❌ includes/auth.php - ERREUR: " . $e->getMessage() . "<br>";
}

try {
    require_once 'includes/security.php';
    echo "✅ includes/security.php - OK<br>";
} catch (Exception $e) {
    echo "❌ includes/security.php - ERREUR: " . $e->getMessage() . "<br>";
}

// Test 3: Vérifier les fonctions utilisées dans register.php
echo "<h2>3. Fonctions Requises</h2>";
$functions = [
    'getSettings' => function_exists('getSettings'),
    'cleanInput' => function_exists('cleanInput'),
    'isValidEmail' => function_exists('isValidEmail'),
    'redirect' => function_exists('redirect'),
    'setFlashMessage' => function_exists('setFlashMessage')
];

foreach ($functions as $func => $exists) {
    echo ($exists ? "✅" : "❌") . " $func()<br>";
}

// Test 4: Vérifier les classes
echo "<h2>4. Classes Requises</h2>";
$classes = [
    'Auth' => class_exists('Auth'),
    'Security' => class_exists('Security')
];

foreach ($classes as $class => $exists) {
    echo ($exists ? "✅" : "❌") . " Classe $class<br>";
}

// Test 5: Test des méthodes Auth
echo "<h2>5. Méthodes Auth</h2>";
if (class_exists('Auth')) {
    try {
        $auth = new Auth();
        $methods = ['isLoggedIn', 'register'];
        foreach ($methods as $method) {
            $exists = method_exists($auth, $method);
            echo ($exists ? "✅" : "❌") . " \$auth->$method()<br>";
        }
    } catch (Exception $e) {
        echo "❌ Erreur création Auth: " . $e->getMessage() . "<br>";
    }
}

// Test 6: Test des méthodes Security
echo "<h2>6. Méthodes Security</h2>";
if (class_exists('Security')) {
    $methods = ['verifyCSRFToken', 'generateCSRFToken', 'isStrongPassword'];
    foreach ($methods as $method) {
        $exists = method_exists('Security', $method);
        echo ($exists ? "✅" : "❌") . " Security::$method()<br>";
    }
}

// Test 7: Test getSettings()
echo "<h2>7. Test getSettings()</h2>";
if (function_exists('getSettings')) {
    try {
        $settings = getSettings();
        echo "✅ getSettings() fonctionne - " . count($settings) . " paramètres<br>";
        
        $regEnabled = ($settings['registration_enabled'] ?? '1') === '1';
        echo ($regEnabled ? "✅" : "❌") . " Inscription activée: " . ($regEnabled ? "OUI" : "NON") . "<br>";
    } catch (Exception $e) {
        echo "❌ getSettings() erreur: " . $e->getMessage() . "<br>";
    }
}

echo "<hr>";
echo "<h2>🚀 Solutions Proposées</h2>";
echo "<p><strong>Si tous les tests sont ✅ :</strong></p>";
echo "<ul>";
echo "<li>Le problème vient peut-être du serveur web (erreur 500)</li>";
echo "<li>Vérifiez les logs d'erreur de votre hébergeur</li>";
echo "<li>Testez <a href='register_simple.php'>register_simple.php</a> qui fonctionne sans dépendances</li>";
echo "</ul>";

echo "<p><strong>Si des tests sont ❌ :</strong></p>";
echo "<ul>";
echo "<li>Nous devons corriger les fichiers manquants ou défaillants</li>";
echo "<li>Ou utiliser la version simplifiée register_simple.php</li>";
echo "</ul>";

echo "<p><strong>Tests disponibles :</strong></p>";
echo "<ul>";
echo "<li><a href='debug_register.php'>debug_register.php</a> - Diagnostic complet</li>";
echo "<li><a href='register_simple.php'>register_simple.php</a> - Version qui fonctionne</li>";
echo "<li><a href='register.php'>register.php</a> - Version originale</li>";
echo "</ul>";
?>