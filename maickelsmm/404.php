<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - MaickelSMM</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #ffffff;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
            text-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
        }
        .error-title {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #ffffff;
        }
        .error-message {
            font-size: 1.1rem;
            color: #b8b8b8;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            margin: 0 10px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
        }
        .btn-secondary:hover {
            box-shadow: 0 10px 20px rgba(78, 205, 196, 0.3);
        }
        .suggestions {
            margin-top: 3rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        .suggestions h3 {
            margin-bottom: 1rem;
            color: #667eea;
        }
        .suggestions ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .suggestions li {
            margin: 0.5rem 0;
        }
        .suggestions a {
            color: #4ecdc4;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .suggestions a:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Page non trouvée</h1>
        <p class="error-message">
            La page que vous recherchez n'existe pas ou a été déplacée. 
            Vérifiez l'URL ou utilisez les liens ci-dessous pour naviguer.
        </p>
        
        <div style="margin-bottom: 2rem;">
            <a href="/" class="btn">Retour à l'accueil</a>
            <a href="/contact.php" class="btn btn-secondary">Nous contacter</a>
        </div>
        
        <div class="suggestions">
            <h3>Pages populaires :</h3>
            <ul>
                <li><a href="/">🏠 Accueil</a></li>
                <li><a href="/login.php">🔐 Connexion</a></li>
                <li><a href="/register.php">📝 Inscription</a></li>
                <li><a href="/contact.php">💬 Contact</a></li>
                <li><a href="/terms.php">📋 Conditions d'utilisation</a></li>
            </ul>
        </div>
    </div>
</body>
</html>