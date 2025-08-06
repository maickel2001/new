<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';

// Rediriger si déjà connecté
if ($auth->isLoggedIn()) {
    redirect('/dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide.';
    } else {
        $email = cleanInput($_POST['email'] ?? '');
        
        if (empty($email)) {
            $error = 'Veuillez saisir votre adresse email.';
        } elseif (!isValidEmail($email)) {
            $error = 'Veuillez saisir une adresse email valide.';
        } else {
            $result = $auth->requestPasswordReset($email);
            if ($result['success']) {
                $success = 'Un email de récupération a été envoyé à votre adresse si elle existe dans notre base de données.';
            } else {
                // Pour des raisons de sécurité, on affiche toujours le même message
                $success = 'Un email de récupération a été envoyé à votre adresse si elle existe dans notre base de données.';
            }
        }
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
    <title>Mot de passe oublié - <?= htmlspecialchars($siteName) ?></title>
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
                <h2>Mot de passe oublié</h2>
                <p>Entrez votre email pour recevoir un lien de récupération</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form method="POST" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i>
                            Adresse email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required 
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            placeholder="votre.email@exemple.com"
                            autofocus
                        >
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="fas fa-paper-plane"></i>
                        Envoyer le lien de récupération
                    </button>
                </form>
            <?php endif; ?>

            <div class="auth-footer">
                <p>Vous vous souvenez de votre mot de passe ?</p>
                <a href="/login.php" class="btn btn-outline">
                    <i class="fas fa-sign-in-alt"></i>
                    Se connecter
                </a>
            </div>

            <div class="back-to-home">
                <a href="/">
                    <i class="fas fa-arrow-left"></i>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-hide success message
        <?php if ($success): ?>
        setTimeout(() => {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }
        }, 10000); // 10 secondes pour laisser le temps de lire
        <?php endif; ?>
    </script>
</body>
</html>