<?php
// Script de diagnostic pour la table users
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration DB - MODIFIEZ LE MOT DE PASSE
$DB_HOST = 'localhost';
$DB_NAME = 'u634930929_Ino';
$DB_USER = 'u634930929_Ino';
$DB_PASS = 'VotreMotDePasse'; // ⚠️ REMPLACEZ par votre mot de passe DB

echo "<h1>🔍 Diagnostic Table Users</h1>";

try {
    $conn = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion DB réussie<br><br>";
} catch(PDOException $e) {
    die("❌ Erreur DB: " . $e->getMessage());
}

// 1. Vérifier si la table existe
echo "<h2>1. Existence de la table</h2>";
try {
    $stmt = $conn->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'users' existe<br>";
    } else {
        echo "❌ Table 'users' n'existe pas<br>";
        die("Arrêt du diagnostic");
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

// 2. Structure de la table
echo "<h2>2. Structure de la table</h2>";
try {
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    
    $hasUsername = false;
    $hasEmail = false;
    
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . $col['Default'] . "</td>";
        echo "<td>" . $col['Extra'] . "</td>";
        echo "</tr>";
        
        if ($col['Field'] === 'username') $hasUsername = true;
        if ($col['Field'] === 'email') $hasEmail = true;
    }
    echo "</table><br>";
    
    echo "<h3>Colonnes importantes :</h3>";
    echo ($hasEmail ? "✅" : "❌") . " Colonne 'email' : " . ($hasEmail ? "PRÉSENTE" : "ABSENTE") . "<br>";
    echo ($hasUsername ? "✅" : "❌") . " Colonne 'username' : " . ($hasUsername ? "PRÉSENTE" : "ABSENTE") . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erreur structure: " . $e->getMessage() . "<br>";
}

// 3. Compter les utilisateurs
echo "<h2>3. Contenu de la table</h2>";
try {
    $count = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "📊 Nombre total d'utilisateurs : <strong>$count</strong><br>";
    
    if ($count > 0) {
        // Afficher quelques utilisateurs (sans mot de passe)
        echo "<h3>Premiers utilisateurs :</h3>";
        $stmt = $conn->query("SELECT id, email, role, status, created_at FROM users LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Rôle</th><th>Status</th><th>Créé le</th></tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>" . $user['status'] . "</td>";
            echo "<td>" . $user['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur contenu: " . $e->getMessage() . "<br>";
}

// 4. Vérifier admin
echo "<h2>4. Utilisateur Admin</h2>";
try {
    $admin = $conn->query("SELECT * FROM users WHERE email = 'admin@maickelsmm.com'")->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ Admin trouvé<br>";
        echo "📧 Email: " . $admin['email'] . "<br>";
        echo "👤 Rôle: " . $admin['role'] . "<br>";
        echo "🔄 Status: " . $admin['status'] . "<br>";
        
        // Test mot de passe
        if (password_verify('password123', $admin['password'])) {
            echo "🔑 Mot de passe 'password123': ✅ CORRECT<br>";
        } else {
            echo "🔑 Mot de passe 'password123': ❌ INCORRECT<br>";
        }
    } else {
        echo "❌ Admin non trouvé<br>";
        echo "<strong>Création de l'admin...</strong><br>";
        
        try {
            $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (email, password, first_name, last_name, role, status, email_verified, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                'admin@maickelsmm.com',
                $hashedPassword,
                'Admin',
                'MaickelSMM',
                'superadmin',
                'active',
                1
            ]);
            echo "✅ Admin créé avec succès !<br>";
        } catch (Exception $e) {
            echo "❌ Erreur création admin: " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur admin: " . $e->getMessage() . "<br>";
}

echo "<h2>🎯 Recommandations</h2>";
if (!$hasUsername) {
    echo "⚠️ La colonne 'username' n'existe pas. Utilisez seulement l'email pour la connexion.<br>";
}
if ($hasEmail) {
    echo "✅ Utilisez <strong>login_minimal.php</strong> avec l'email uniquement.<br>";
}

echo "<br><a href='login_minimal.php' style='background: #00d4ff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Tester la Connexion</a>";
?>