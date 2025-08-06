<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';

// Rediriger si déjà connecté
if ($auth->isLoggedIn()) {
    redirect('/dashboard.php');
}

$token = cleanInput($_GET['token'] ?? '');
$error = '';
$success = '';

// Vérifier le token
if (empty($token)) {
    $error = 'Token de réinitialisation manquant.';
} else {
    // Vérifier la validité du token
    $tokenValid = $auth->verifyPasswordResetToken($token);
    if (!$tokenValid) {
        $error = 'Token de réinitialisation invalide ou expiré.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        
        // Vérifier la force du mot de passe
        if (!Security::isStrongPassword($password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.';
        }
        
        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        } else {
            $result = $auth->resetPassword($token, $password);
            if ($result['success']) {
                $success = 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.';
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
    <title>Réinitialiser mot de passe - <?= htmlspecialchars($siteName) ?></title>
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
                <h2>Nouveau mot de passe</h2>
                <p>Définissez votre nouveau mot de passe</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
                
                <div class="auth-footer">
                    <a href="/login.php" class="btn btn-primary btn-full">
                        <i class="fas fa-sign-in-alt"></i>
                        Se connecter maintenant
                    </a>
                </div>
            <?php elseif (empty($error)): ?>
                <form method="POST" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Nouveau mot de passe
                        </label>
                        <div class="password-input">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                placeholder="Minimum 6 caractères"
                                minlength="6"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">
                            <i class="fas fa-lock"></i>
                            Confirmer le mot de passe
                        </label>
                        <div class="password-input">
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                required 
                                placeholder="Répétez le nouveau mot de passe"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="fas fa-key"></i>
                        Réinitialiser le mot de passe
                    </button>
                </form>
            <?php endif; ?>

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

        // Vérification de la force du mot de passe
        document.getElementById('password')?.addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('password-strength');
            
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const levels = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
            const colors = ['#ff4444', '#ff8800', '#ffbb00', '#88cc00', '#00cc44'];
            
            if (password.length > 0) {
                strengthDiv.innerHTML = `
                    <div class="strength-bar">
                        <div class="strength-fill" style="width: ${strength * 20}%; background: ${colors[strength - 1] || '#ff4444'}"></div>
                    </div>
                    <span style="color: ${colors[strength - 1] || '#ff4444'}">${levels[strength - 1] || 'Très faible'}</span>
                `;
            } else {
                strengthDiv.innerHTML = '';
            }
        });

        // Vérification de la correspondance des mots de passe
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    this.style.borderColor = '#00cc44';
                } else {
                    this.style.borderColor = '#ff4444';
                }
            } else {
                this.style.borderColor = '';
            }
        });

        // Auto-redirect après succès
        <?php if ($success): ?>
        setTimeout(() => {
            window.location.href = '/login.php';
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>