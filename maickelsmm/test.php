<?php
/**
 * Fichier de test pour diagnostiquer les problèmes MaickelSMM
 * À supprimer après installation réussie
 */

echo "<h1>🔧 Test de Configuration MaickelSMM</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;background:#f5f5f5;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} pre{background:#fff;padding:1rem;border-radius:5px;}</style>";

// Test 1: Version PHP
echo "<h2>1. Version PHP</h2>";
$phpVersion = phpversion();
if (version_compare($phpVersion, '7.4.0', '>=')) {
    echo "<p class='ok'>✅ PHP $phpVersion (Compatible)</p>";
} else {
    echo "<p class='error'>❌ PHP $phpVersion (Minimum requis: 7.4)</p>";
}

// Test 2: Extensions PHP
echo "<h2>2. Extensions PHP Requises</h2>";
$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'curl'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='ok'>✅ $ext</p>";
    } else {
        echo "<p class='error'>❌ $ext (Manquant)</p>";
    }
}

// Test 3: Permissions des dossiers
echo "<h2>3. Permissions des Dossiers</h2>";
$directories = [
    'assets/uploads/',
    'assets/uploads/payments/',
    'assets/uploads/banners/'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p class='ok'>✅ $dir (Écriture autorisée)</p>";
        } else {
            echo "<p class='error'>❌ $dir (Pas d'écriture)</p>";
        }
    } else {
        echo "<p class='warning'>⚠️ $dir (Dossier manquant)</p>";
    }
}

// Test 4: Fichiers de configuration
echo "<h2>4. Fichiers de Configuration</h2>";
$configFiles = [
    'config/database.php',
    'config/config.php',
    'includes/functions.php',
    'includes/auth.php'
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        echo "<p class='ok'>✅ $file</p>";
    } else {
        echo "<p class='error'>❌ $file (Manquant)</p>";
    }
}

// Test 5: Connexion à la base de données
echo "<h2>5. Test de Connexion Base de Données</h2>";
try {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        
        echo "<p class='info'>📋 Configuration détectée:</p>";
        echo "<pre>";
        echo "Host: " . (defined('DB_HOST') ? DB_HOST : 'Non défini') . "\n";
        echo "Base: " . (defined('DB_NAME') ? DB_NAME : 'Non défini') . "\n";
        echo "User: " . (defined('DB_USER') ? DB_USER : 'Non défini') . "\n";
        echo "Pass: " . (defined('DB_PASS') ? (DB_PASS ? '***masqué***' : 'Vide') : 'Non défini') . "\n";
        echo "</pre>";
        
        $db = Database::getInstance();
        if ($db->testConnection()) {
            echo "<p class='ok'>✅ Connexion à la base de données réussie</p>";
            
            // Test des tables
            $tables = ['users', 'services', 'categories', 'orders', 'settings'];
            foreach ($tables as $table) {
                try {
                    $result = $db->fetchOne("SELECT COUNT(*) as count FROM $table");
                    echo "<p class='ok'>✅ Table '$table' : {$result['count']} enregistrements</p>";
                } catch (Exception $e) {
                    echo "<p class='error'>❌ Table '$table' : Erreur</p>";
                }
            }
        } else {
            echo "<p class='error'>❌ Connexion à la base de données échouée</p>";
        }
    } else {
        echo "<p class='error'>❌ Fichier de configuration database.php manquant</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur de connexion: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 6: Variables d'environnement
echo "<h2>6. Variables Serveur</h2>";
echo "<pre>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . "\n";
echo "Server Name: " . ($_SERVER['SERVER_NAME'] ?? 'Non défini') . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Non défini') . "\n";
echo "PHP Self: " . ($_SERVER['PHP_SELF'] ?? 'Non défini') . "\n";
echo "</pre>";

// Test 7: Limites PHP
echo "<h2>7. Limites PHP</h2>";
echo "<pre>";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "\n";
echo "Post Max Size: " . ini_get('post_max_size') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "s\n";
echo "Display Errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "\n";
echo "</pre>";

// Instructions finales
echo "<h2>🚀 Instructions</h2>";
echo "<div style='background:#e3f2fd;padding:1rem;border-radius:5px;'>";
echo "<p><strong>Si tous les tests sont ✅ :</strong></p>";
echo "<ul>";
echo "<li>Supprimez ce fichier test.php</li>";
echo "<li>Accédez à votre site : <a href='index.php'>index.php</a></li>";
echo "<li>Connectez-vous à l'admin : <a href='admin/'>admin/</a></li>";
echo "</ul>";

echo "<p><strong>Si vous avez des erreurs ❌ :</strong></p>";
echo "<ul>";
echo "<li>Corrigez la configuration dans config/database.php</li>";
echo "<li>Vérifiez les permissions des dossiers (755 ou 777)</li>";
echo "<li>Contactez votre hébergeur si nécessaire</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><small>Test effectué le " . date('Y-m-d H:i:s') . " | MaickelSMM v1.0</small></p>";
?>