<?php
/**
 * Script pour diagnostiquer et corriger le problème avec la table categories
 */

echo "<h1>🔧 Fix Categories - MaickelSMM</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .ok{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:1rem;}</style>";

require_once 'config/database.php';
$db = Database::getInstance();

// Test 1: Vérifier la structure de la table categories
echo "<h2>1. Structure de la table 'categories'</h2>";
try {
    $structure = $db->fetchAll("DESCRIBE categories");
    echo "<p class='ok'>✅ Table 'categories' accessible</p>";
    echo "<pre>";
    foreach ($structure as $col) {
        echo "- {$col['Field']} : {$col['Type']} " . ($col['Null'] == 'NO' ? '(NOT NULL)' : '(NULL)') . "\n";
    }
    echo "</pre>";
    
    // Vérifier si la colonne 'status' existe
    $hasStatus = false;
    foreach ($structure as $col) {
        if ($col['Field'] === 'status') {
            $hasStatus = true;
            break;
        }
    }
    
    if (!$hasStatus) {
        echo "<p class='error'>❌ La colonne 'status' n'existe pas dans la table 'categories'</p>";
        echo "<p class='warning'>⚠️ Ceci explique pourquoi la requête échoue !</p>";
    } else {
        echo "<p class='ok'>✅ La colonne 'status' existe</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur structure: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Test 2: Tester différentes requêtes sur categories
echo "<h2>2. Tests de requêtes sur 'categories'</h2>";

// Test requête simple
try {
    $categories = $db->fetchAll("SELECT * FROM categories ORDER BY id ASC");
    echo "<p class='ok'>✅ Requête simple - " . count($categories) . " catégories trouvées</p>";
    
    foreach ($categories as $cat) {
        echo "<p>- ID: {$cat['id']}, Nom: {$cat['name']}";
        if (isset($cat['status'])) {
            echo ", Status: {$cat['status']}";
        }
        echo "</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur requête simple: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Test requête avec status (si la colonne existe)
if ($hasStatus) {
    try {
        $activeCategories = $db->fetchAll("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC, name ASC");
        echo "<p class='ok'>✅ Requête avec status - " . count($activeCategories) . " catégories actives</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur requête avec status: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p class='warning'>⚠️ Test avec status ignoré (colonne inexistante)</p>";
}

// Test 3: Proposer une fonction getCategories() corrigée
echo "<h2>3. Fonction getCategories() Corrigée</h2>";

if ($hasStatus) {
    echo "<p class='ok'>✅ Votre table a la colonne 'status', la fonction originale devrait marcher</p>";
    echo "<p class='warning'>⚠️ Le problème vient peut-être des données. Vérifiez les valeurs dans la colonne 'status'</p>";
} else {
    echo "<p class='warning'>⚠️ Votre table n'a PAS la colonne 'status'</p>";
    echo "<p>Fonction corrigée à utiliser :</p>";
    echo "<pre>";
    echo "function getCategories(\$active_only = true) {\n";
    echo "    \$db = Database::getInstance();\n";
    echo "    // Version sans colonne 'status'\n";
    echo "    return \$db->fetchAll(\"SELECT * FROM categories ORDER BY sort_order ASC, name ASC\");\n";
    echo "}\n";
    echo "</pre>";
}

// Test 4: Solution temporaire
echo "<h2>4. Test de la solution</h2>";

function getCategoriesFixed($active_only = true) {
    global $db, $hasStatus;
    
    if ($hasStatus) {
        $where = $active_only ? "WHERE status = 'active'" : "";
        return $db->fetchAll("SELECT * FROM categories $where ORDER BY sort_order ASC, name ASC");
    } else {
        // Pas de colonne status, on retourne toutes les catégories
        return $db->fetchAll("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");
    }
}

try {
    $categories = getCategoriesFixed(true);
    echo "<p class='ok'>✅ Fonction corrigée fonctionne - " . count($categories) . " catégories</p>";
    
    echo "<h3>Catégories disponibles :</h3>";
    foreach ($categories as $cat) {
        echo "<p>- {$cat['name']} (ID: {$cat['id']})";
        if (isset($cat['icon'])) {
            echo " <i class='{$cat['icon']}'></i>";
        }
        echo "</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Même la fonction corrigée échoue: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h2>🚀 Solution</h2>";
echo "<p><strong>Le problème est maintenant identifié !</strong></p>";
if (!$hasStatus) {
    echo "<p>Votre table 'categories' n'a pas de colonne 'status', mais la fonction PHP essaie de l'utiliser.</p>";
    echo "<p><strong>Solution :</strong> Modifier la fonction getCategories() dans includes/functions.php</p>";
} else {
    echo "<p>Votre table 'categories' a la colonne 'status', le problème vient peut-être des données.</p>";
    echo "<p><strong>Solution :</strong> Vérifier les valeurs dans la colonne 'status'</p>";
}
?>