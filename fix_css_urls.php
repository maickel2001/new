<?php
echo "<h1>🔧 Correction des URLs CSS</h1>";

// Détecter l'URL actuelle
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$currentUrl = $protocol . '://' . $host;

echo "<h2>1. Détection Automatique</h2>";
echo "📋 URL actuelle détectée: <strong>$currentUrl</strong><br>";

// Lire le config.php actuel
$configPath = 'config/config.php';
if (file_exists($configPath)) {
    $configContent = file_get_contents($configPath);
    echo "✅ Fichier config.php trouvé<br>";
    
    // Extraire l'URL actuelle
    if (preg_match("/define\('SITE_URL', '([^']+)'\)/", $configContent, $matches)) {
        $currentSiteUrl = $matches[1];
        echo "📋 URL actuelle dans config: <strong>$currentSiteUrl</strong><br>";
        
        if ($currentSiteUrl !== $currentUrl) {
            echo "<h2>2. Correction Nécessaire</h2>";
            echo "❌ L'URL dans config.php ne correspond pas à votre domaine<br>";
            echo "🔧 Correction automatique...<br>";
            
            // Remplacer l'URL
            $newConfigContent = preg_replace(
                "/define\('SITE_URL', '[^']+'\)/",
                "define('SITE_URL', '$currentUrl')",
                $configContent
            );
            
            // Sauvegarder
            if (file_put_contents($configPath, $newConfigContent)) {
                echo "✅ URL corrigée dans config.php<br>";
                echo "📋 Nouvelle URL: <strong>$currentUrl</strong><br>";
            } else {
                echo "❌ Erreur lors de la sauvegarde<br>";
            }
        } else {
            echo "✅ URL déjà correcte<br>";
        }
    } else {
        echo "❌ Impossible de trouver SITE_URL dans config.php<br>";
    }
} else {
    echo "❌ Fichier config.php introuvable<br>";
}

echo "<h2>3. Test des URLs CSS</h2>";

// URLs à tester
$cssUrls = [
    './assets/css/style.css',
    '/assets/css/style.css',
    'assets/css/style.css',
    $currentUrl . '/assets/css/style.css'
];

foreach ($cssUrls as $url) {
    echo "🔗 <a href='$url' target='_blank'>Tester: $url</a><br>";
}

echo "<h2>4. Vérification du Fichier CSS</h2>";
if (file_exists('assets/css/style.css')) {
    $cssSize = filesize('assets/css/style.css');
    echo "✅ Fichier style.css existe ($cssSize octets)<br>";
    
    // Vérifier les permissions
    if (is_readable('assets/css/style.css')) {
        echo "✅ Fichier CSS lisible<br>";
    } else {
        echo "❌ Fichier CSS non lisible - Problème de permissions<br>";
    }
} else {
    echo "❌ Fichier style.css manquant<br>";
}

echo "<h2>5. Test de la Page avec CSS Intégré</h2>";
echo "<p>Si le CSS externe ne fonctionne pas, testez cette version avec CSS intégré :</p>";
echo "<p><a href='index_with_css.php' target='_blank' style='color: #00d4ff; font-weight: bold;'>🎨 Tester index_with_css.php</a></p>";

echo "<h2>6. Solutions Proposées</h2>";
echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<h3>Solution 1: CSS Intégré (Rapide)</h3>";
echo "<p>Utilisez <code>index_with_css.php</code> - Fonctionne immédiatement</p>";
echo "</div>";

echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<h3>Solution 2: Corriger les URLs</h3>";
echo "<p>1. L'URL a été corrigée automatiquement ci-dessus</p>";
echo "<p>2. Testez maintenant: <a href='index.php'>index.php</a></p>";
echo "</div>";

echo "<div style='background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<h3>Solution 3: Vérifier .htaccess</h3>";
echo "<p>Le fichier .htaccess peut bloquer les assets</p>";
echo "<p>Testez les liens CSS ci-dessus pour voir lequel fonctionne</p>";
echo "</div>";

echo "<hr>";
echo "<h2>🚀 Actions Recommandées</h2>";
echo "<ol>";
echo "<li><strong>Testez immédiatement</strong>: <a href='index_with_css.php'>index_with_css.php</a></li>";
echo "<li><strong>Si ça marche</strong>: Le problème vient des URLs CSS externes</li>";
echo "<li><strong>Si ça ne marche pas</strong>: Problème plus profond (serveur, PHP, etc.)</li>";
echo "<li><strong>Partagez les résultats</strong> pour que je puisse vous aider davantage</li>";
echo "</ol>";

// CSS inline pour styliser cette page
echo "<style>";
echo "body { font-family: Arial, sans-serif; background: #1a1a2e; color: white; padding: 20px; }";
echo "h1, h2 { color: #00d4ff; }";
echo "a { color: #ff6b6b; }";
echo "code { background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px; }";
echo "</style>";
?>