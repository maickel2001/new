<?php
/**
 * Diagnostic avancé des tables MaickelSMM
 * Pour identifier les problèmes de structure
 */

require_once 'config/database.php';

echo "<h1>🔍 Diagnostic Avancé des Tables</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;background:#f5f5f5;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} pre{background:#fff;padding:1rem;border-radius:5px;overflow-x:auto;}</style>";

try {
    $db = Database::getInstance();
    
    echo "<h2>1. Structure de la table 'services'</h2>";
    
    try {
        $columns = $db->fetchAll("DESCRIBE services");
        echo "<p class='ok'>✅ Table 'services' accessible</p>";
        echo "<p class='info'>📋 Structure actuelle :</p>";
        echo "<pre>";
        foreach ($columns as $col) {
            echo "- {$col['Field']} : {$col['Type']} " . ($col['Null'] == 'NO' ? '(NOT NULL)' : '(NULL)') . "\n";
        }
        echo "</pre>";
        
        // Tenter une requête simple
        try {
            $count = $db->fetchOne("SELECT COUNT(*) as count FROM services");
            echo "<p class='ok'>✅ Requête COUNT réussie : {$count['count']} enregistrements</p>";
            
            // Tester une insertion simple
            try {
                $db->execute("INSERT INTO services (category_id, name, description, min_quantity, max_quantity, price_per_1000, delivery_time, guarantee, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                [1, 'Test Service', 'Description test', 100, 1000, 500.00, '1 jour', 'yes', 1, 'active']);
                echo "<p class='ok'>✅ Insertion test réussie</p>";
                
                // Supprimer le test
                $db->execute("DELETE FROM services WHERE name = 'Test Service'");
                echo "<p class='info'>ℹ️ Test supprimé</p>";
                
            } catch (Exception $e) {
                echo "<p class='error'>❌ Erreur d'insertion : " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p class='warning'>⚠️ Problème probable : Structure de table incorrecte</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur COUNT : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Table 'services' inaccessible : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<h2>2. Structure de la table 'settings'</h2>";
    
    try {
        $columns = $db->fetchAll("DESCRIBE settings");
        echo "<p class='ok'>✅ Table 'settings' accessible</p>";
        echo "<p class='info'>📋 Structure actuelle :</p>";
        echo "<pre>";
        foreach ($columns as $col) {
            echo "- {$col['Field']} : {$col['Type']} " . ($col['Null'] == 'NO' ? '(NOT NULL)' : '(NULL)') . "\n";
        }
        echo "</pre>";
        
        // Tenter une requête simple
        try {
            $count = $db->fetchOne("SELECT COUNT(*) as count FROM settings");
            echo "<p class='ok'>✅ Requête COUNT réussie : {$count['count']} enregistrements</p>";
            
            // Tester une insertion simple
            try {
                $db->execute("INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)", 
                ['test_key', 'test_value', 'text', 'Test setting']);
                echo "<p class='ok'>✅ Insertion test réussie</p>";
                
                // Supprimer le test
                $db->execute("DELETE FROM settings WHERE setting_key = 'test_key'");
                echo "<p class='info'>ℹ️ Test supprimé</p>";
                
            } catch (Exception $e) {
                echo "<p class='error'>❌ Erreur d'insertion : " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p class='warning'>⚠️ Problème probable : Structure de table incorrecte</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur COUNT : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Table 'settings' inaccessible : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<h2>3. Vérification des autres tables</h2>";
    
    $tables = ['users', 'categories', 'orders'];
    foreach ($tables as $table) {
        try {
            $columns = $db->fetchAll("DESCRIBE $table");
            $count = $db->fetchOne("SELECT COUNT(*) as count FROM $table");
            echo "<p class='ok'>✅ Table '$table' : {$count['count']} enregistrements, " . count($columns) . " colonnes</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Table '$table' : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<h2>4. Script de reconstruction des tables</h2>";
    
    echo "<div style='background:#fff3cd;border:1px solid #ffeaa7;padding:1rem;border-radius:5px;margin:2rem 0;'>";
    echo "<h3 style='color:#856404;margin:0 0 1rem 0;'>🔨 Solution : Reconstruction des tables</h3>";
    echo "<p style='color:#856404;'>Si les structures sont incorrectes, nous devons recréer les tables.</p>";
    echo "<p style='color:#856404;'><strong>Cliquez sur le lien ci-dessous pour reconstruire automatiquement :</strong></p>";
    echo "<p><a href='rebuild_tables.php' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>🔨 Reconstruire les tables</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur générale : " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><small>Diagnostic effectué le " . date('Y-m-d H:i:s') . " | MaickelSMM v1.0</small></p>";
?>