<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';

// Rediriger si déjà connecté
if ($auth->isLoggedIn()) {
    redirect('/dashboard.php');
}

// Vérifier si les inscriptions sont activées
$settings = getSettings();
$registrationEnabled = ($settings['registration_enabled'] ?? '1') === '1';

if (!$registrationEnabled) {
    setFlashMessage('error', 'Les inscriptions sont temporairement fermées.');
    redirect('/login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide.';
    } else {
        $data = [
            'username' => cleanInput($_POST['username'] ?? ''),
            'email' => cleanInput($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'first_name' => cleanInput($_POST['first_name'] ?? ''),
            'last_name' => cleanInput($_POST['last_name'] ?? ''),
            'phone' => cleanInput($_POST['phone'] ?? ''),
            'terms_accepted' => isset($_POST['terms_accepted'])
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères.';
        }
        
        if (!isValidEmail($data['email'])) {
            $errors[] = 'Veuillez saisir un email valide.';
        }
        
        if (strlen($data['password']) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }
        
        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        
        if (empty($data['first_name']) || empty($data['last_name'])) {
            $errors[] = 'Le prénom et le nom sont obligatoires.';
        }
        
        if (!$data['terms_accepted']) {
            $errors[] = 'Vous devez accepter les conditions d\'utilisation.';
        }
        
        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        } else {
            $result = $auth->register($data);
            if ($result['success']) {
                $success = 'Compte créé avec succès ! Vérifiez votre email pour activer votre compte.';
                // Réinitialiser le formulaire
                $_POST = [];
            } else {
                $error = $result['message'];
            }
        }
    }
}

$siteName = $settings['site_name'] ?? 'MaickelSMM';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?= htmlspecialchars($siteName) ?></title>
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
                <h2>Créer votre compte</h2>
                <p>Rejoignez notre communauté SMM</p>
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
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">
                            <i class="fas fa-user"></i>
                            Prénom *
                        </label>
                        <input 
                            type="text" 
                            id="first_name" 
                            name="first_name" 
                            required 
                            value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                            placeholder="Votre prénom"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">
                            <i class="fas fa-user"></i>
                            Nom *
                        </label>
                        <input 
                            type="text" 
                            id="last_name" 
                            name="last_name" 
                            required 
                            value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
                            placeholder="Votre nom"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-at"></i>
                        Nom d'utilisateur *
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        placeholder="Nom d'utilisateur unique"
                        minlength="3"
                    >
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Adresse email *
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        placeholder="votre.email@exemple.com"
                    >
                </div>

                <div class="form-group">
                    <label for="phone">
                        <i class="fas fa-phone"></i>
                        Téléphone
                    </label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                        placeholder="+33 6 12 34 56 78"
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Mot de passe *
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
                            Confirmer *
                        </label>
                        <div class="password-input">
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                required 
                                placeholder="Répétez le mot de passe"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms_accepted" required>
                        <span class="checkmark"></span>
                        J'accepte les <a href="/terms.php" target="_blank">conditions d'utilisation</a> 
                        et la <a href="/privacy.php" target="_blank">politique de confidentialité</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-user-plus"></i>
                    Créer mon compte
                </button>
            </form>

            <div class="auth-footer">
                <p>Déjà un compte ?</p>
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
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('password-strength');
            
            let strength = 0;
            let feedback = [];
            
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
        document.getElementById('confirm_password').addEventListener('input', function() {
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
    </script>
</body>
</html>