<?php
// Script de test pour vérifier la configuration Hostinger
require_once 'config/database-hostinger.php';

// Headers pour affichage web
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Configuration CREE 2GK - Hostinger</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #22c55e; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        .info { color: #3b82f6; font-weight: bold; }
        .test-item { margin: 15px 0; padding: 15px; border-left: 4px solid #e5e7eb; background: #f9fafb; }
        .test-item.success { border-color: #22c55e; background: #f0fdf4; }
        .test-item.error { border-color: #ef4444; background: #fef2f2; }
        .test-item.warning { border-color: #f59e0b; background: #fffbeb; }
        pre { background: #1f2937; color: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h1 { color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        h2 { color: #374151; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Test Configuration CREE 2GK sur Hostinger</h1>
        
        <h2>📊 Informations Serveur</h2>
        <?php
        $info = getHostingerInfo();
        echo '<div class="test-item info">';
        echo '<strong>Informations du serveur :</strong><br>';
        echo '<pre>' . print_r($info, true) . '</pre>';
        echo '</div>';
        ?>

        <h2>🔌 Test de Connexion Base de Données</h2>
        <?php
        try {
            $testConnection = testDatabaseConnection();
            if ($testConnection) {
                echo '<div class="test-item success">';
                echo '<span class="success">✅ Connexion à la base de données réussie !</span><br>';
                echo 'Host: ' . DB_HOST . '<br>';
                echo 'Database: ' . DB_NAME . '<br>';
                echo 'User: ' . DB_USER;
                echo '</div>';
                
                // Test des tables
                echo '<h3>📋 Vérification des Tables</h3>';
                $db = getDB();
                $tables = ['users', 'categories', 'products', 'orders', 'cart'];
                
                foreach ($tables as $table) {
                    try {
                        $stmt = $db->query("SHOW TABLES LIKE '$table'");
                        $exists = $stmt->rowCount() > 0;
                        
                        if ($exists) {
                            $countStmt = $db->query("SELECT COUNT(*) as count FROM $table");
                            $count = $countStmt->fetch()['count'];
                            echo '<div class="test-item success">';
                            echo "<span class=\"success\">✅ Table '$table' existe</span> ($count enregistrements)";
                            echo '</div>';
                        } else {
                            echo '<div class="test-item error">';
                            echo "<span class=\"error\">❌ Table '$table' manquante</span>";
                            echo '</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="test-item error">';
                        echo "<span class=\"error\">❌ Erreur table '$table':</span> " . $e->getMessage();
                        echo '</div>';
                    }
                }
                
            } else {
                echo '<div class="test-item error">';
                echo '<span class="error">❌ Échec de la connexion à la base de données</span><br>';
                echo 'Vérifiez vos paramètres dans config/database-hostinger.php';
                echo '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="test-item error">';
            echo '<span class="error">❌ Erreur de connexion :</span><br>';
            echo $e->getMessage();
            echo '</div>';
        }
        ?>

        <h2>🔧 Extensions PHP Requises</h2>
        <?php
        $requiredExtensions = [
            'pdo' => 'PDO (Base de données)',
            'pdo_mysql' => 'PDO MySQL',
            'curl' => 'cURL (API externes)',
            'openssl' => 'OpenSSL (Sécurité)',
            'json' => 'JSON',
            'mbstring' => 'Multibyte String',
            'session' => 'Sessions'
        ];

        foreach ($requiredExtensions as $ext => $description) {
            $loaded = extension_loaded($ext);
            echo '<div class="test-item ' . ($loaded ? 'success' : 'error') . '">';
            echo ($loaded ? '✅' : '❌') . " $description: ";
            echo '<span class="' . ($loaded ? 'success' : 'error') . '">';
            echo $loaded ? 'Activé' : 'Manquant';
            echo '</span></div>';
        }
        ?>

        <h2>📁 Permissions Fichiers</h2>
        <?php
        $directories = [
            'assets/css' => 'Fichiers CSS',
            'assets/js' => 'Fichiers JavaScript', 
            'api' => 'Scripts API',
            'config' => 'Configuration',
            'logs' => 'Logs (sera créé)'
        ];

        foreach ($directories as $dir => $description) {
            $path = __DIR__ . '/' . $dir;
            $exists = is_dir($path);
            $writable = $exists ? is_writable($path) : false;
            
            if (!$exists && $dir === 'logs') {
                // Créer le dossier logs s'il n'existe pas
                mkdir($path, 0755, true);
                $exists = true;
                $writable = is_writable($path);
            }
            
            echo '<div class="test-item ' . ($exists && $writable ? 'success' : ($exists ? 'warning' : 'error')) . '">';
            echo ($exists ? '✅' : '❌') . " $description ($dir): ";
            
            if ($exists) {
                echo '<span class="' . ($writable ? 'success' : 'warning') . '">';
                echo $writable ? 'Lecture/Écriture OK' : 'Lecture seule';
                echo '</span>';
            } else {
                echo '<span class="error">Dossier manquant</span>';
            }
            echo '</div>';
        }
        ?>

        <h2>🌐 Test API Endpoints</h2>
        <?php
        $apiEndpoints = [
            'api/products.php' => 'API Produits',
            'api/categories.php' => 'API Catégories',
            'api/cart.php' => 'API Panier'
        ];

        foreach ($apiEndpoints as $endpoint => $description) {
            $path = __DIR__ . '/' . $endpoint;
            $exists = file_exists($path);
            
            echo '<div class="test-item ' . ($exists ? 'success' : 'error') . '">';
            echo ($exists ? '✅' : '❌') . " $description: ";
            echo '<span class="' . ($exists ? 'success' : 'error') . '">';
            echo $exists ? 'Fichier présent' : 'Fichier manquant';
            echo '</span>';
            
            if ($exists) {
                echo '<br><small>URL: <code>https://votre-domaine.com/' . $endpoint . '</code></small>';
            }
            echo '</div>';
        }
        ?>

        <h2>📝 Instructions de Déploiement</h2>
        <div class="test-item info">
            <strong>Étapes suivantes :</strong><br>
            1. <strong>Configurez la base de données</strong> dans hPanel Hostinger<br>
            2. <strong>Modifiez</strong> <code>config/database-hostinger.php</code> avec vos vraies informations<br>
            3. <strong>Importez</strong> <code>database/schema.sql</code> via phpMyAdmin<br>
            4. <strong>Renommez</strong> <code>config/database-hostinger.php</code> en <code>config/database.php</code><br>
            5. <strong>Supprimez</strong> ce fichier <code>test-connection.php</code> après les tests<br>
            6. <strong>Activez SSL</strong> dans hPanel pour HTTPS
        </div>

        <h2>🔒 Sécurité</h2>
        <div class="test-item warning">
            <strong>⚠️ Important :</strong><br>
            • Changez toutes les clés secrètes dans la configuration<br>
            • Activez HTTPS/SSL sur votre domaine<br>
            • Supprimez ce fichier de test après vérification<br>
            • Configurez des sauvegardes automatiques<br>
            • Surveillez les logs d'erreur régulièrement
        </div>

        <div style="text-align: center; margin-top: 40px; padding: 20px; background: #f3f4f6; border-radius: 10px;">
            <strong>🎉 CREE 2GK prêt pour Hostinger !</strong><br>
            <small>Supprimez ce fichier après avoir terminé la configuration</small>
        </div>
    </div>
</body>
</html>