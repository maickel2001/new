<?php
session_start();
session_destroy();

// Supprimer les cookies de "se souvenir de moi" s'ils existent
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion - MaickelSMM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .logout-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 3rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        h1 {
            color: #00d4ff;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        p {
            color: #b0b3c1;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(45deg, #00d4ff, #ff6b6b);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 0 10px;
            transition: transform 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
    <script>
        // Redirection automatique après 3 secondes
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 3000);
    </script>
</head>
<body>
    <div class="logout-container">
        <h1>✅ Déconnexion Réussie</h1>
        <p>Vous avez été déconnecté avec succès de votre compte MaickelSMM.</p>
        <p style="font-size: 0.9rem;">Redirection automatique vers l'accueil dans 3 secondes...</p>
        
        <div>
            <a href="index.php" class="btn">Retour à l'accueil</a>
            <a href="login_ultra_simple.php" class="btn btn-secondary">Se reconnecter</a>
        </div>
    </div>
</body>
</html>