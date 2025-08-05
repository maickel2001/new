<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';

// Vérifier l'authentification
$auth->requireAuth();

$user = $auth->getCurrentUser();
$settings = getSettings();
$siteName = $settings['site_name'] ?? 'MaickelSMM';

$error = '';
$success = '';

// Traitement du formulaire de mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide.';
    } else {
        $data = [
            'first_name' => cleanInput($_POST['first_name'] ?? ''),
            'last_name' => cleanInput($_POST['last_name'] ?? ''),
            'email' => cleanInput($_POST['email'] ?? ''),
            'phone' => cleanInput($_POST['phone'] ?? ''),
            'username' => cleanInput($_POST['username'] ?? '')
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['first_name']) || empty($data['last_name'])) {
            $errors[] = 'Le prénom et le nom sont obligatoires.';
        }
        
        if (!isValidEmail($data['email'])) {
            $errors[] = 'Veuillez saisir un email valide.';
        }
        
        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors[] = 'Le nom d\'utilisateur doit contenir au moins 3 caractères.';
        }
        
        // Vérifier l'unicité de l'email et du nom d'utilisateur
        $db = Database::getInstance();
        
        if ($data['email'] !== $user['email']) {
            $existingEmail = $db->fetchOne("SELECT id FROM users WHERE email = ? AND id != ?", [$data['email'], $user['id']]);
            if ($existingEmail) {
                $errors[] = 'Cet email est déjà utilisé par un autre compte.';
            }
        }
        
        if ($data['username'] !== $user['username']) {
            $existingUsername = $db->fetchOne("SELECT id FROM users WHERE username = ? AND id != ?", [$data['username'], $user['id']]);
            if ($existingUsername) {
                $errors[] = 'Ce nom d\'utilisateur est déjà pris.';
            }
        }
        
        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        } else {
            // Mettre à jour le profil
            $updated = $db->execute("
                UPDATE users 
                SET first_name = ?, last_name = ?, email = ?, phone = ?, username = ?, updated_at = NOW()
                WHERE id = ?
            ", [$data['first_name'], $data['last_name'], $data['email'], $data['phone'], $data['username'], $user['id']]);
            
            if ($updated) {
                $success = 'Profil mis à jour avec succès !';
                // Recharger les données utilisateur
                $user = $auth->getCurrentUser(true); // Force refresh
            } else {
                $error = 'Erreur lors de la mise à jour du profil.';
            }
        }
    }
}

// Traitement du formulaire de changement de mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide.';
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($currentPassword)) {
            $errors[] = 'Veuillez saisir votre mot de passe actuel.';
        } elseif (!verifyPassword($currentPassword, $user['password'])) {
            $errors[] = 'Le mot de passe actuel est incorrect.';
        }
        
        if (strlen($newPassword) < 6) {
            $errors[] = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Les nouveaux mots de passe ne correspondent pas.';
        }
        
        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        } else {
            $hashedPassword = hashPassword($newPassword);
            $db = Database::getInstance();
            
            $updated = $db->execute("
                UPDATE users 
                SET password = ?, updated_at = NOW()
                WHERE id = ?
            ", [$hashedPassword, $user['id']]);
            
            if ($updated) {
                $success = 'Mot de passe modifié avec succès !';
            } else {
                $error = 'Erreur lors de la modification du mot de passe.';
            }
        }
    }
}

// Messages flash
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - <?= htmlspecialchars($siteName) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
</head>
<body class="dashboard-page">
    <!-- Navigation -->
    <nav class="dashboard-nav">
        <div class="nav-brand">
            <i class="fas fa-rocket"></i>
            <span><?= htmlspecialchars($siteName) ?></span>
        </div>
        
        <div class="nav-menu">
            <a href="/dashboard.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="/orders.php" class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Mes commandes</span>
            </a>
            <a href="/profile.php" class="nav-item active">
                <i class="fas fa-user"></i>
                <span>Mon profil</span>
            </a>
            <a href="/" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Accueil</span>
            </a>
        </div>

        <div class="nav-user">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <span class="user-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                    <span class="user-role"><?= ucfirst($user['role']) ?></span>
                </div>
            </div>
            <div class="user-actions">
                <a href="/logout.php" class="btn btn-outline btn-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="dashboard-main">
        <div class="dashboard-header">
            <h1>
                <i class="fas fa-user"></i>
                Mon profil
            </h1>
            <p>Gérez vos informations personnelles et paramètres de compte</p>
        </div>

        <?php if ($flashMessage): ?>
            <div class="alert alert-<?= $flashMessage['type'] ?>">
                <i class="fas fa-<?= $flashMessage['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($flashMessage['message']) ?>
            </div>
        <?php endif; ?>

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

        <div class="profile-content">
            <!-- Informations du compte -->
            <div class="profile-section">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-user-edit"></i>
                        Informations personnelles
                    </h2>
                    <p>Modifiez vos informations de base</p>
                </div>

                <form method="POST" class="profile-form">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="update_profile" value="1">
                    
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
                                value="<?= htmlspecialchars($user['first_name']) ?>"
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
                                value="<?= htmlspecialchars($user['last_name']) ?>"
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
                            value="<?= htmlspecialchars($user['username']) ?>"
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
                            value="<?= htmlspecialchars($user['email']) ?>"
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
                            value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                            placeholder="+33 6 12 34 56 78"
                        >
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- Changement de mot de passe -->
            <div class="profile-section">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-lock"></i>
                        Sécurité du compte
                    </h2>
                    <p>Modifiez votre mot de passe</p>
                </div>

                <form method="POST" class="profile-form">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="form-group">
                        <label for="current_password">
                            <i class="fas fa-lock"></i>
                            Mot de passe actuel *
                        </label>
                        <div class="password-input">
                            <input 
                                type="password" 
                                id="current_password" 
                                name="current_password" 
                                required 
                                placeholder="Votre mot de passe actuel"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-lock"></i>
                                Nouveau mot de passe *
                            </label>
                            <div class="password-input">
                                <input 
                                    type="password" 
                                    id="new_password" 
                                    name="new_password" 
                                    required 
                                    placeholder="Minimum 6 caractères"
                                    minlength="6"
                                >
                                <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength" id="password-strength"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-lock"></i>
                                Confirmer le nouveau *
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
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key"></i>
                            Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>

            <!-- Informations du compte -->
            <div class="profile-section">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-info-circle"></i>
                        Informations du compte
                    </h2>
                    <p>Détails de votre compte</p>
                </div>

                <div class="account-info">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar-alt"></i>
                                Membre depuis
                            </div>
                            <div class="info-value">
                                <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-shield-alt"></i>
                                Statut du compte
                            </div>
                            <div class="info-value">
                                <span class="status status-<?= strtolower($user['status']) ?>">
                                    <?= ucfirst($user['status']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-envelope-check"></i>
                                Email vérifié
                            </div>
                            <div class="info-value">
                                <?php if ($user['email_verified']): ?>
                                    <span class="text-success">
                                        <i class="fas fa-check-circle"></i>
                                        Vérifié
                                    </span>
                                <?php else: ?>
                                    <span class="text-warning">
                                        <i class="fas fa-clock"></i>
                                        En attente
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-user-tag"></i>
                                Rôle
                            </div>
                            <div class="info-value">
                                <span class="role-badge role-<?= $user['role'] ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
        document.getElementById('new_password').addEventListener('input', function() {
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
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword.length > 0) {
                if (newPassword === confirmPassword) {
                    this.style.borderColor = '#00cc44';
                } else {
                    this.style.borderColor = '#ff4444';
                }
            } else {
                this.style.borderColor = '';
            }
        });

        // Auto-hide flash messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>