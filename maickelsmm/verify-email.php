<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';

$token = cleanInput($_GET['token'] ?? '');
$error = '';
$success = '';

if (empty($token)) {
    $error = 'Token de vérification manquant.';
} else {
    $result = $auth->verifyEmail($token);
    if ($result['success']) {
        $success = 'Votre email a été vérifié avec succès ! Vous pouvez maintenant vous connecter.';
    } else {
        $error = $result['message'];
    }
}

$settings = getSettings();
$siteName = $settings['site_name'] ?? 'MaickelSMM';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification email - <?= htmlspecialchars($siteName) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <i class="fas fa-rocket"></i>
                    <h1><?= htmlspecialchars($siteName) ?></h1>
                </div>
                <h2>Vérification d'email</h2>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                
                <div class="verification-info">
                    <h3>Problème de vérification ?</h3>
                    <p>Si votre lien de vérification a expiré ou ne fonctionne pas :</p>
                    <ul>
                        <li>Vérifiez que vous avez cliqué sur le bon lien</li>
                        <li>Assurez-vous que le lien n'est pas tronqué</li>
                        <li>Contactez notre support si le problème persiste</li>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
                
                <div class="verification-success">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Email vérifié !</h3>
                    <p>Votre compte est maintenant activé. Vous pouvez accéder à tous nos services SMM.</p>
                </div>
            <?php endif; ?>

            <div class="auth-footer">
                <?php if ($success): ?>
                    <a href="/login.php" class="btn btn-primary btn-full">
                        <i class="fas fa-sign-in-alt"></i>
                        Se connecter maintenant
                    </a>
                <?php else: ?>
                    <a href="/register.php" class="btn btn-outline">
                        <i class="fas fa-user-plus"></i>
                        Créer un compte
                    </a>
                    <a href="/contact.php" class="btn btn-primary">
                        <i class="fas fa-headset"></i>
                        Contacter le support
                    </a>
                <?php endif; ?>
            </div>

            <div class="back-to-home">
                <a href="/">
                    <i class="fas fa-arrow-left"></i>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <style>
        .verification-info {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .verification-info h3 {
            color: var(--text-primary);
            margin: 0 0 1rem 0;
            font-size: 1.1rem;
        }
        
        .verification-info p {
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }
        
        .verification-info ul {
            color: var(--text-secondary);
            margin: 0;
            padding-left: 1.5rem;
        }
        
        .verification-info li {
            margin-bottom: 0.5rem;
        }
        
        .verification-success {
            text-align: center;
            padding: 2rem 0;
        }
        
        .success-icon {
            font-size: 4rem;
            color: #10b981;
            margin-bottom: 1rem;
        }
        
        .verification-success h3 {
            color: var(--text-primary);
            margin: 0 0 1rem 0;
            font-size: 1.5rem;
        }
        
        .verification-success p {
            color: var(--text-secondary);
            margin: 0;
        }
        
        .auth-footer {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
    </style>

    <script>
        // Auto-redirect vers login après succès
        <?php if ($success): ?>
        setTimeout(() => {
            window.location.href = '/login.php';
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>