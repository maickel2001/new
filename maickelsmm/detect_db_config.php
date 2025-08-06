<?php
// Script pour détecter automatiquement la configuration DB
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Détection Configuration Base de Données</h1>";

// 1. Essayer de lire depuis config.php
echo "<h2>1. Vérification config.php</h2>";
$configFound = false;
$dbConfig = [];

if (file_exists('config/config.php')) {
    echo "✅ Fichier config/config.php trouvé<br>";
    
    $configContent = file_get_contents('config/config.php');
    
    // Extraire les constantes DB
    if (preg_match("/define\('DB_HOST',\s*'([^']+)'\)/", $configContent, $matches)) {
        $dbConfig['host'] = $matches[1];
        echo "📍 DB_HOST: " . $matches[1] . "<br>";
    }
    if (preg_match("/define\('DB_NAME',\s*'([^']+)'\)/", $configContent, $matches)) {
        $dbConfig['name'] = $matches[1];
        echo "🗃️ DB_NAME: " . $matches[1] . "<br>";
    }
    if (preg_match("/define\('DB_USER',\s*'([^']+)'\)/", $configContent, $matches)) {
        $dbConfig['user'] = $matches[1];
        echo "👤 DB_USER: " . $matches[1] . "<br>";
    }
    if (preg_match("/define\('DB_PASS',\s*'([^']*)'\)/", $configContent, $matches)) {
        $dbConfig['pass'] = $matches[1];
        echo "🔑 DB_PASS: " . ($matches[1] ? "***" . substr($matches[1], -2) : "VIDE") . "<br>";
    }
    
    $configFound = count($dbConfig) >= 4;
} else {
    echo "❌ Fichier config/config.php non trouvé<br>";
}

// 2. Essayer de lire depuis database.php
echo "<h2>2. Vérification database.php</h2>";
if (file_exists('config/database.php')) {
    echo "✅ Fichier config/database.php trouvé<br>";
    
    try {
        // Capturer la sortie pour éviter les erreurs d'affichage
        ob_start();
        include 'config/database.php';
        ob_end_clean();
        
        // Vérifier si les constantes sont définies
        if (defined('DB_HOST')) {
            $dbConfig['host'] = DB_HOST;
            echo "📍 DB_HOST: " . DB_HOST . "<br>";
        }
        if (defined('DB_NAME')) {
            $dbConfig['name'] = DB_NAME;
            echo "🗃️ DB_NAME: " . DB_NAME . "<br>";
        }
        if (defined('DB_USER')) {
            $dbConfig['user'] = DB_USER;
            echo "👤 DB_USER: " . DB_USER . "<br>";
        }
        if (defined('DB_PASS')) {
            $dbConfig['pass'] = DB_PASS;
            echo "🔑 DB_PASS: " . (DB_PASS ? "***" . substr(DB_PASS, -2) : "VIDE") . "<br>";
        }
        
        $configFound = count($dbConfig) >= 4;
    } catch (Exception $e) {
        echo "⚠️ Erreur lors de la lecture: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Fichier config/database.php non trouvé<br>";
}

// 3. Essayer avec les variables d'environnement
echo "<h2>3. Variables d'Environnement</h2>";
$envVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DATABASE_URL'];
$envFound = false;

foreach ($envVars as $var) {
    $value = getenv($var);
    if ($value) {
        echo "✅ $var: " . ($var === 'DB_PASS' ? "***" . substr($value, -2) : $value) . "<br>";
        $envFound = true;
        
        if ($var === 'DB_HOST') $dbConfig['host'] = $value;
        if ($var === 'DB_NAME') $dbConfig['name'] = $value;
        if ($var === 'DB_USER') $dbConfig['user'] = $value;
        if ($var === 'DB_PASS') $dbConfig['pass'] = $value;
    }
}

if (!$envFound) {
    echo "❌ Aucune variable d'environnement trouvée<br>";
}

// 4. Test de connexion
echo "<h2>4. Test de Connexion</h2>";
if (isset($dbConfig['host'], $dbConfig['name'], $dbConfig['user'], $dbConfig['pass'])) {
    echo "🔧 Tentative de connexion avec les paramètres détectés...<br>";
    
    try {
        $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']}";
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ <strong>CONNEXION RÉUSSIE !</strong><br>";
        
        // Tester une requête simple
        $count = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{$dbConfig['name']}'")->fetchColumn();
        echo "📊 Nombre de tables dans la base: <strong>$count</strong><br>";
        
        // Vérifier la table users
        $userTableExists = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{$dbConfig['name']}' AND TABLE_NAME = 'users'")->fetchColumn();
        echo "👥 Table 'users': " . ($userTableExists ? "✅ EXISTE" : "❌ ABSENTE") . "<br>";
        
    } catch (PDOException $e) {
        echo "❌ <strong>ÉCHEC DE CONNEXION:</strong> " . $e->getMessage() . "<br>";
    }
} else {
    echo "⚠️ Configuration incomplète détectée<br>";
}

// 5. Génération du code corrigé
echo "<h2>5. Code Corrigé pour vos Fichiers</h2>";
if (isset($dbConfig['host'], $dbConfig['name'], $dbConfig['user'], $dbConfig['pass'])) {
    echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; font-family: monospace;'>";
    echo "// Configuration DB - COPIEZ CES LIGNES<br>";
    echo "\$DB_HOST = '" . htmlspecialchars($dbConfig['host']) . "';<br>";
    echo "\$DB_NAME = '" . htmlspecialchars($dbConfig['name']) . "';<br>";
    echo "\$DB_USER = '" . htmlspecialchars($dbConfig['user']) . "';<br>";
    echo "\$DB_PASS = '" . htmlspecialchars($dbConfig['pass']) . "';<br>";
    echo "</div><br>";
    
    echo "<strong>📋 Instructions:</strong><br>";
    echo "1. Copiez ces lignes dans vos fichiers minimaux<br>";
    echo "2. Remplacez les lignes existantes dans:<br>";
    echo "&nbsp;&nbsp;&nbsp;• login_minimal.php<br>";
    echo "&nbsp;&nbsp;&nbsp;• admin_minimal.php<br>";
    echo "&nbsp;&nbsp;&nbsp;• dashboard_minimal.php<br>";
    echo "&nbsp;&nbsp;&nbsp;• check_users_table.php<br>";
} else {
    echo "❌ <strong>Configuration DB non détectée automatiquement</strong><br>";
    echo "<strong>🔧 Solution manuelle:</strong><br>";
    echo "1. Vérifiez votre panneau Hostinger<br>";
    echo "2. Notez les informations de connexion DB<br>";
    echo "3. Modifiez manuellement les fichiers minimaux<br>";
}

echo "<br><div style='background: #e3f2fd; padding: 15px; border-radius: 5px;'>";
echo "<strong>💡 Conseil Hostinger:</strong><br>";
echo "Dans votre panneau Hostinger → Bases de données → Gérer<br>";
echo "Vous trouverez: Nom d'hôte, Nom de la base, Nom d'utilisateur, Mot de passe";
echo "</div>";
?>