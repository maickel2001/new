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
        $login = cleanInput($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        if (empty($login) || empty($password)) {
            $error = 'Veuillez remplir tous les champs.';
        } else {
            $result = $auth->login($login, $password, $remember);
            if ($result['success']) {
                $success = 'Connexion réussie ! Redirection...';
                setFlashMessage('success', 'Bienvenue ' . $result['user']['first_name'] . ' !');
                
                // Redirection selon le rôle
                $redirectUrl = in_array($result['user']['role'], ['admin', 'superadmin']) 
                    ? '/admin/' 
                    : '/dashboard.php';
                    
                echo '<script>setTimeout(() => window.location.href = "' . $redirectUrl . '", 1500);</script>';
            } else {
                $error = $result['message'];
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
    <title>Connexion - <?= htmlspecialchars($siteName) ?></title>
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
                <h2>Connexion à votre compte</h2>
                <p>Accédez à votre espace personnel</p>
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

            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                
                <div class="form-group">
                    <label for="login">
                        <i class="fas fa-user"></i>
                        Email ou nom d'utilisateur
                    </label>
                    <input 
                        type="text" 
                        id="login" 
                        name="login" 
                        required 
                        value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                        placeholder="Votre email ou nom d'utilisateur"
                    >
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Mot de passe
                    </label>
                    <div class="password-input">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            placeholder="Votre mot de passe"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span class="checkmark"></span>
                        Se souvenir de moi
                    </label>
                    <a href="/forgot-password.php" class="forgot-link">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-sign-in-alt"></i>
                    Se connecter
                </button>
            </form>

            <div class="auth-footer">
                <p>Pas encore de compte ?</p>
                <a href="/register.php" class="btn btn-outline">
                    <i class="fas fa-user-plus"></i>
                    Créer un compte
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
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // Auto-hide success message and redirect
        <?php if ($success): ?>
        setTimeout(() => {
            const alert = document.querySelector('.alert-success');
            if (alert) alert.style.display = 'none';
        }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>