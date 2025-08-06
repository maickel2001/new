<?php
/**
 * Page de debug pour identifier le problème exact
 */

echo "<h1>🔍 Debug MaickelSMM</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .ok{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:1rem;}</style>";

// Test 1: Configuration
echo "<h2>1. Test Configuration</h2>";
try {
    require_once 'config/config.php';
    echo "<p class='ok'>✅ config.php chargé</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur config.php: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Test 2: Database
echo "<h2>2. Test Database</h2>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    echo "<p class='ok'>✅ Database connectée</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur database: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Test 3: Functions
echo "<h2>3. Test Functions</h2>";
try {
    require_once 'includes/functions.php';
    echo "<p class='ok'>✅ functions.php chargé</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur functions.php: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Test 4: getSettings()
echo "<h2>4. Test getSettings()</h2>";
try {
    $settings = getSettings();
    echo "<p class='ok'>✅ getSettings() - " . count($settings) . " paramètres</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur getSettings(): " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>Requête: SELECT setting_key, setting_value, setting_type FROM settings WHERE 1</pre>";
}

// Test 5: getCategories()
echo "<h2>5. Test getCategories()</h2>";
try {
    $categories = getCategories(true);
    echo "<p class='ok'>✅ getCategories() - " . count($categories) . " catégories</p>";
    
    // Afficher les catégories
    foreach ($categories as $cat) {
        echo "<p>- {$cat['name']} (ID: {$cat['id']})</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur getCategories(): " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>Requête: SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC, name ASC</pre>";
}

// Test 6: getAllServices()
echo "<h2>6. Test getAllServices()</h2>";
try {
    $services = getAllServices(true);
    echo "<p class='ok'>✅ getAllServices() - " . count($services) . " services</p>";
    
    // Afficher quelques services
    $count = 0;
    foreach ($services as $service) {
        if ($count < 3) {
            echo "<p>- {$service['name']} (Catégorie: {$service['category_name']})</p>";
            $count++;
        }
    }
    if (count($services) > 3) {
        echo "<p>... et " . (count($services) - 3) . " autres services</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur getAllServices(): " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>Requête: SELECT s.*, c.name as category_name, c.icon as category_icon FROM services s JOIN categories c ON s.category_id = c.id WHERE s.status = 'active' ORDER BY c.sort_order ASC, s.sort_order ASC, s.name ASC</pre>";
    
    // Test alternatif sans JOIN
    echo "<h3>Test alternatif sans JOIN:</h3>";
    try {
        $services_simple = $db->fetchAll("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order ASC, name ASC LIMIT 5");
        echo "<p class='ok'>✅ Services sans JOIN - " . count($services_simple) . " services</p>";
        foreach ($services_simple as $service) {
            echo "<p>- {$service['name']} (ID: {$service['id']}, Cat ID: {$service['category_id']})</p>";
        }
    } catch (Exception $e2) {
        echo "<p class='error'>❌ Même les services simples échouent: " . htmlspecialchars($e2->getMessage()) . "</p>";
    }
}

// Test 7: Vérification des tables
echo "<h2>7. Vérification des Tables</h2>";
$tables = ['categories', 'services'];
foreach ($tables as $table) {
    try {
        $count = $db->fetchOne("SELECT COUNT(*) as count FROM $table");
        echo "<p class='ok'>✅ Table '$table' : {$count['count']} enregistrements</p>";
        
        // Vérifier la structure
        $structure = $db->fetchAll("DESCRIBE $table");
        echo "<details><summary>Structure de $table (" . count($structure) . " colonnes)</summary>";
        echo "<pre>";
        foreach ($structure as $col) {
            echo "- {$col['Field']} : {$col['Type']}\n";
        }
        echo "</pre></details>";
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur table '$table': " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<hr>";
echo "<p><strong>Conclusion :</strong> Le problème exact est maintenant identifié ci-dessus.</p>";
echo "<p><a href='simple_test.php'>← Retour au test simple</a></p>";
?>