<?php
/**
 * Diagnostic spécifique pour les services
 */

echo "<h1>🔍 Debug Services - MaickelSMM</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .ok{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:1rem;}</style>";

require_once 'config/database.php';
$db = Database::getInstance();

// Test 1: Structure de la table services
echo "<h2>1. Structure de la table 'services'</h2>";
try {
    $structure = $db->fetchAll("DESCRIBE services");
    echo "<p class='ok'>✅ Table 'services' accessible</p>";
    echo "<pre>";
    foreach ($structure as $col) {
        echo "- {$col['Field']} : {$col['Type']} " . ($col['Null'] == 'NO' ? '(NOT NULL)' : '(NULL)') . "\n";
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur structure services: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Test 2: Compter les services
echo "<h2>2. Test COUNT sur services</h2>";
try {
    $count = $db->fetchOne("SELECT COUNT(*) as count FROM services");
    echo "<p class='ok'>✅ COUNT services : {$count['count']} services</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur COUNT services: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 3: Services simples sans filtre
echo "<h2>3. Test services simples (sans filtre)</h2>";
try {
    $services = $db->fetchAll("SELECT * FROM services LIMIT 3");
    echo "<p class='ok'>✅ Services simples - " . count($services) . " services</p>";
    foreach ($services as $service) {
        echo "<p>- {$service['name']} (ID: {$service['id']}, Cat: {$service['category_id']})";
        if (isset($service['status'])) {
            echo " Status: {$service['status']}";
        }
        echo "</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur services simples: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 4: Services avec filtre status
echo "<h2>4. Test services avec status = 'active'</h2>";
try {
    $activeServices = $db->fetchAll("SELECT * FROM services WHERE status = 'active' LIMIT 3");
    echo "<p class='ok'>✅ Services actifs - " . count($activeServices) . " services</p>";
    foreach ($activeServices as $service) {
        echo "<p>- {$service['name']} (Status: {$service['status']})</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur services actifs: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 5: Test du JOIN avec categories
echo "<h2>5. Test JOIN services + categories</h2>";
try {
    $joinServices = $db->fetchAll("
        SELECT s.name as service_name, c.name as category_name 
        FROM services s 
        JOIN categories c ON s.category_id = c.id 
        LIMIT 3
    ");
    echo "<p class='ok'>✅ JOIN simple - " . count($joinServices) . " services</p>";
    foreach ($joinServices as $service) {
        echo "<p>- {$service['service_name']} → {$service['category_name']}</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur JOIN simple: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test 6: Test du JOIN avec filtres
echo "<h2>6. Test JOIN avec filtres</h2>";
try {
    $filteredServices = $db->fetchAll("
        SELECT s.name as service_name, c.name as category_name, s.status, c.is_active
        FROM services s 
        JOIN categories c ON s.category_id = c.id 
        WHERE s.status = 'active' AND c.is_active = 1
        LIMIT 3
    ");
    echo "<p class='ok'>✅ JOIN avec filtres - " . count($filteredServices) . " services</p>";
    foreach ($filteredServices as $service) {
        echo "<p>- {$service['service_name']} → {$service['category_name']} (S:{$service['status']}, C:{$service['is_active']})</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur JOIN avec filtres: " . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Test alternatif : vérifier les category_id orphelins
    echo "<h3>Test alternatif : Vérification des category_id</h3>";
    try {
        $orphans = $db->fetchAll("
            SELECT s.id, s.name, s.category_id 
            FROM services s 
            LEFT JOIN categories c ON s.category_id = c.id 
            WHERE c.id IS NULL
            LIMIT 5
        ");
        if (count($orphans) > 0) {
            echo "<p class='error'>❌ Services avec category_id invalides :</p>";
            foreach ($orphans as $orphan) {
                echo "<p>- Service '{$orphan['name']}' (ID: {$orphan['id']}) → category_id: {$orphan['category_id']} (inexistant)</p>";
            }
        } else {
            echo "<p class='ok'>✅ Aucun service orphelin trouvé</p>";
        }
    } catch (Exception $e2) {
        echo "<p class='error'>❌ Erreur test orphelins: " . htmlspecialchars($e2->getMessage()) . "</p>";
    }
}

// Test 7: Test de la fonction corrigée
echo "<h2>7. Test fonction getAllServices() corrigée</h2>";

require_once 'includes/functions.php';

try {
    $services = getAllServices(true);
    echo "<p class='ok'>✅ getAllServices() corrigée - " . count($services) . " services</p>";
    
    if (count($services) > 0) {
        echo "<h3>Premiers services :</h3>";
        $count = 0;
        foreach ($services as $service) {
            if ($count < 5) {
                echo "<p>- {$service['name']} → {$service['category_name']} (Prix: {$service['price_per_1000']} FCFA)</p>";
                $count++;
            }
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur getAllServices() corrigée: " . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Test avec la version sans filtre de catégorie
    echo "<h3>Test sans filtre de catégorie :</h3>";
    try {
        $servicesNoFilter = $db->fetchAll("
            SELECT s.*, c.name as category_name, c.icon as category_icon
            FROM services s 
            JOIN categories c ON s.category_id = c.id 
            WHERE s.status = 'active'
            LIMIT 5
        ");
        echo "<p class='ok'>✅ Services sans filtre catégorie - " . count($servicesNoFilter) . " services</p>";
    } catch (Exception $e2) {
        echo "<p class='error'>❌ Même sans filtre catégorie: " . htmlspecialchars($e2->getMessage()) . "</p>";
    }
}

echo "<hr>";
echo "<h2>🎯 Conclusion</h2>";
echo "<p>Le problème exact est maintenant identifié ci-dessus.</p>";
echo "<p>Si le JOIN échoue, il y a probablement des services avec des category_id qui n'existent pas dans la table categories.</p>";
?>