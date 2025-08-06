<?php
echo "<h1>🎨 Diagnostic CSS - MaickelSMM</h1>";

// Test 1: Vérification des fichiers CSS
echo "<h2>1. Fichiers CSS</h2>";
$cssFiles = [
    'assets/css/style.css' => file_exists('assets/css/style.css'),
    'assets/css/' => is_dir('assets/css/'),
    'assets/' => is_dir('assets/')
];

foreach ($cssFiles as $file => $exists) {
    echo ($exists ? "✅" : "❌") . " $file<br>";
    if ($exists && !is_dir($file)) {
        $size = filesize($file);
        echo "&nbsp;&nbsp;&nbsp;&nbsp;Taille: " . number_format($size) . " octets<br>";
    }
}

// Test 2: Contenu du fichier CSS
echo "<h2>2. Contenu CSS</h2>";
if (file_exists('assets/css/style.css')) {
    $cssContent = file_get_contents('assets/css/style.css');
    $lines = substr_count($cssContent, "\n");
    echo "✅ Fichier CSS contient $lines lignes<br>";
    
    // Vérifier les règles importantes
    $rules = [
        'body' => strpos($cssContent, 'body') !== false,
        '.header' => strpos($cssContent, '.header') !== false,
        '.hero' => strpos($cssContent, '.hero') !== false,
        '.service-card' => strpos($cssContent, '.service-card') !== false,
        'CSS variables' => strpos($cssContent, '--bg-primary') !== false
    ];
    
    foreach ($rules as $rule => $found) {
        echo ($found ? "✅" : "❌") . " Règle $rule<br>";
    }
} else {
    echo "❌ Fichier style.css introuvable<br>";
}

// Test 3: Configuration des URLs
echo "<h2>3. Configuration URLs</h2>";
try {
    if (file_exists('config/config.php')) {
        require_once 'config/config.php';
        
        if (defined('ASSETS_URL')) {
            echo "✅ ASSETS_URL définie: " . ASSETS_URL . "<br>";
        } else {
            echo "❌ ASSETS_URL non définie<br>";
        }
        
        if (defined('SITE_URL')) {
            echo "✅ SITE_URL définie: " . SITE_URL . "<br>";
        } else {
            echo "❌ SITE_URL non définie<br>";
        }
    } else {
        echo "❌ config.php introuvable<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur config: " . $e->getMessage() . "<br>";
}

// Test 4: Test d'accès direct au CSS
echo "<h2>4. Test d'Accès CSS</h2>";
$cssUrl = '/assets/css/style.css';
echo "📋 URL CSS théorique: <a href='$cssUrl' target='_blank'>$cssUrl</a><br>";

// Test avec différentes URLs
$possibleUrls = [
    './assets/css/style.css',
    '/assets/css/style.css',
    'assets/css/style.css'
];

foreach ($possibleUrls as $url) {
    echo "🔗 <a href='$url' target='_blank'>Tester: $url</a><br>";
}

// Test 5: Permissions des fichiers
echo "<h2>5. Permissions</h2>";
if (file_exists('assets/css/style.css')) {
    $perms = fileperms('assets/css/style.css');
    $perms_octal = substr(sprintf('%o', $perms), -4);
    echo "📋 Permissions style.css: $perms_octal<br>";
    
    if (is_readable('assets/css/style.css')) {
        echo "✅ Fichier CSS lisible<br>";
    } else {
        echo "❌ Fichier CSS non lisible<br>";
    }
} else {
    echo "❌ Impossible de vérifier les permissions<br>";
}

// Test 6: Structure du dossier assets
echo "<h2>6. Structure Assets</h2>";
if (is_dir('assets')) {
    echo "✅ Dossier assets/ existe<br>";
    
    $dirs = ['css', 'js', 'images', 'uploads'];
    foreach ($dirs as $dir) {
        $path = "assets/$dir";
        echo (is_dir($path) ? "✅" : "❌") . " $path/<br>";
    }
} else {
    echo "❌ Dossier assets/ manquant<br>";
}

// Test 7: Analyse de index.php
echo "<h2>7. Inclusion CSS dans index.php</h2>";
if (file_exists('index.php')) {
    $indexContent = file_get_contents('index.php');
    
    $checks = [
        'ASSETS_URL' => strpos($indexContent, 'ASSETS_URL') !== false,
        'style.css' => strpos($indexContent, 'style.css') !== false,
        '<link' => strpos($indexContent, '<link') !== false,
        'stylesheet' => strpos($indexContent, 'stylesheet') !== false
    ];
    
    foreach ($checks as $check => $found) {
        echo ($found ? "✅" : "❌") . " $check dans index.php<br>";
    }
    
    // Extraire la ligne CSS
    if (preg_match('/<link[^>]*href="([^"]*style\.css[^"]*)"[^>]*>/i', $indexContent, $matches)) {
        echo "📋 Lien CSS trouvé: <code>" . htmlspecialchars($matches[0]) . "</code><br>";
        echo "📋 URL CSS: <code>" . htmlspecialchars($matches[1]) . "</code><br>";
    } else {
        echo "❌ Aucun lien CSS trouvé dans index.php<br>";
    }
} else {
    echo "❌ index.php introuvable<br>";
}

echo "<hr>";
echo "<h2>🚀 Solutions Proposées</h2>";

echo "<h3>Solution 1: CSS Inline (Test Rapide)</h3>";
echo "<p><a href='index_with_css.php' target='_blank'>Tester avec CSS intégré</a></p>";

echo "<h3>Solution 2: Vérifier les URLs</h3>";
echo "<ul>";
echo "<li>Vérifiez que ASSETS_URL dans config.php correspond à votre structure</li>";
echo "<li>Testez les liens CSS ci-dessus pour voir lequel fonctionne</li>";
echo "</ul>";

echo "<h3>Solution 3: Re-créer le CSS</h3>";
echo "<p>Si le fichier CSS est corrompu, nous le re-créerons</p>";

echo "<h3>🔧 Tests à Faire</h3>";
echo "<ol>";
echo "<li>Cliquez sur les liens CSS ci-dessus pour voir s'ils s'ouvrent</li>";
echo "<li>Testez <a href='index_with_css.php'>index_with_css.php</a></li>";
echo "<li>Partagez-moi les résultats</li>";
echo "</ol>";
?>