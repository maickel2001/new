<?php
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur 500 - MaickelSMM</title>
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
            color: #ff6b6b;
            margin-bottom: 1rem;
            text-shadow: 0 0 20px rgba(255, 107, 107, 0.3);
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
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .support-info {
            margin-top: 3rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        .support-info h3 {
            margin-bottom: 1rem;
            color: #667eea;
        }
        .support-info p {
            margin: 0.5rem 0;
            color: #b8b8b8;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <h1 class="error-title">Erreur Interne du Serveur</h1>
        <p class="error-message">
            Une erreur technique s'est produite sur notre serveur. 
            Nos équipes ont été automatiquement notifiées et travaillent à résoudre le problème.
        </p>
        
        <a href="/" class="btn">Retour à l'accueil</a>
        
        <div class="support-info">
            <h3>Besoin d'aide ?</h3>
            <p><strong>Email :</strong> contact@maickelsmm.com</p>
            <p><strong>WhatsApp :</strong> +225 07 12 34 56 78</p>
            <p>Veuillez mentionner l'erreur 500 dans votre message.</p>
        </div>
    </div>
</body>
</html>