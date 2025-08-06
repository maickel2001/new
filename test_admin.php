<?php
echo "<h1>🔐 Test Utilisateur Admin</h1>";

try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    echo "✅ Connexion à la base de données OK<br><br>";
    
    // Chercher l'utilisateur admin
    $admin = $db->fetchOne("SELECT * FROM users WHERE email = 'admin@maickelsmm.com'");
    
    if ($admin) {
        echo "<h2>👤 Utilisateur Admin Trouvé</h2>";
        echo "📧 <strong>Email</strong>: " . htmlspecialchars($admin['email']) . "<br>";
        echo "👤 <strong>Username</strong>: " . htmlspecialchars($admin['username']) . "<br>";
        echo "🏷️ <strong>Rôle</strong>: " . htmlspecialchars($admin['role']) . "<br>";
        echo "📊 <strong>Statut</strong>: " . htmlspecialchars($admin['status']) . "<br>";
        echo "📅 <strong>Créé le</strong>: " . htmlspecialchars($admin['created_at']) . "<br>";
        echo "✉️ <strong>Email vérifié</strong>: " . ($admin['email_verified'] ? 'Oui' : 'Non') . "<br><br>";
        
        // Test du mot de passe
        echo "<h2>🔑 Test du Mot de Passe</h2>";
        if (password_verify('password123', $admin['password'])) {
            echo "✅ <strong>Mot de passe correct</strong> : password123<br>";
        } else {
            echo "❌ <strong>Mot de passe incorrect</strong><br>";
            echo "Le mot de passe par défaut 'password123' ne fonctionne pas.<br>";
        }
        
        echo "<hr>";
        echo "<h2>🚀 Connexion Admin</h2>";
        echo "<p><strong>URL Admin</strong> : <a href='admin/' target='_blank'>admin/</a></p>";
        echo "<p><strong>Login Simple</strong> : <a href='login_simple.php' target='_blank'>login_simple.php</a></p>";
        echo "<p><strong>Login Original</strong> : <a href='login.php' target='_blank'>login.php</a></p>";
        
    } else {
        echo "<h2>❌ Utilisateur Admin Non Trouvé</h2>";
        echo "<p>L'utilisateur admin n'existe pas dans la base de données.</p>";
        
        // Vérifier s'il y a des utilisateurs
        $userCount = $db->fetchOne("SELECT COUNT(*) as count FROM users");
        echo "<p>Nombre total d'utilisateurs : " . $userCount['count'] . "</p>";
        
        if ($userCount['count'] > 0) {
            echo "<h3>Utilisateurs existants :</h3>";
            $users = $db->fetchAll("SELECT id, username, email, role, status FROM users LIMIT 5");
            foreach ($users as $user) {
                echo "- " . htmlspecialchars($user['username']) . " (" . htmlspecialchars($user['email']) . ") - " . htmlspecialchars($user['role']) . "<br>";
            }
        }
        
        echo "<hr>";
        echo "<h2>🔧 Solution</h2>";
        echo "<p>Exécutez le script <a href='repair_database.php' target='_blank'>repair_database.php</a> pour créer l'utilisateur admin.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Erreur</h2>";
    echo "<p>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Vérifiez votre configuration de base de données dans <code>config/database.php</code></p>";
}

// CSS pour styliser
echo "<style>";
echo "body { font-family: Arial, sans-serif; background: #1a1a2e; color: white; padding: 20px; }";
echo "h1, h2 { color: #00d4ff; }";
echo "a { color: #ff6b6b; }";
echo "code { background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px; }";
echo "hr { border: 1px solid rgba(255,255,255,0.2); margin: 20px 0; }";
echo "</style>";
?>