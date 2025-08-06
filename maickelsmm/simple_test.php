<?php
// Test ultra-simple sans dépendances
echo "<h1>🚀 Test Simple MaickelSMM</h1>";
echo "<p>✅ PHP fonctionne parfaitement !</p>";
echo "<p>📅 " . date('Y-m-d H:i:s') . "</p>";
echo "<p>🌐 Serveur : " . ($_SERVER['SERVER_NAME'] ?? 'Non défini') . "</p>";
echo "<p>📍 Chemin : " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . "</p>";

// Test simple de la base de données
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    $result = $db->fetchOne("SELECT COUNT(*) as count FROM services");
    echo "<p>✅ Base de données : {$result['count']} services</p>";
} catch (Exception $e) {
    echo "<p>❌ Erreur DB : " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>🏠 Tester la page d'accueil</a></p>";
echo "<p><a href='admin/'>⚙️ Tester l'admin</a></p>";
?>