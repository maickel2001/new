<?php
// Version ultra-simple sans dépendances
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration DB - MODIFIEZ CES VALEURS
$host = 'localhost';
$dbname = 'u634930929_Ino';
$username = 'u634930929_Ino'; 
$password = '';

$error = '';
$success = '';

// Connexion DB simple
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("DB Error: " . $e->getMessage());
}

// Traitement du formulaire
if ($_POST) {
    $login = trim($_POST['login'] ?? '');
    $pass = $_POST['password'] ?? '';
    
    if ($login && $pass) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) AND status = 'active'");
            $stmt->execute([$login, $login]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($pass, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['first_name'];
                
                if (in_array($user['role'], ['admin', 'superadmin'])) {
                    header('Location: admin/index.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                $error = 'Identifiants incorrects';
            }
        } catch (Exception $e) {
            $error = 'Erreur: ' . $e->getMessage();
        }
    } else {
        $error = 'Veuillez remplir tous les champs';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion Simple</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial; background: #1a1a2e; color: white; padding: 50px; }
        .form { max-width: 400px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 30px; border-radius: 10px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 12px; background: #00d4ff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: #ff6b6b; margin: 10px 0; }
        .success { color: #00d4ff; margin: 10px 0; }
        h1 { text-align: center; color: #00d4ff; }
    </style>
</head>
<body>
    <div class="form">
        <h1>🔐 Connexion Simple</h1>
        
        <?php if ($error): ?>
            <div class="error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="login" placeholder="Email ou nom d'utilisateur" 
                   value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            <strong>Admin par défaut :</strong><br>
            Email: admin@maickelsmm.com<br>
            Mot de passe: password123
        </p>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="index.php" style="color: #00d4ff;">← Retour à l'accueil</a>
        </p>
    </div>
</body>
</html>