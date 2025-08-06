<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/security.php';

// Vérifier l'authentification admin
$auth->requireAuth('admin');

$user = $auth->getCurrentUser();
$settings = getSettings();
$siteName = $settings['site_name'] ?? 'MaickelSMM';

$db = Database::getInstance();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Token de sécurité invalide.');
        redirect('/admin/settings.php');
    }
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_general') {
        $updates = [
            'site_name' => cleanInput($_POST['site_name'] ?? ''),
            'site_description' => cleanInput($_POST['site_description'] ?? ''),
            'contact_email' => cleanInput($_POST['contact_email'] ?? ''),
            'contact_phone' => cleanInput($_POST['contact_phone'] ?? ''),
            'whatsapp_number' => cleanInput($_POST['whatsapp_number'] ?? ''),
            'currency' => cleanInput($_POST['currency'] ?? 'XOF'),
            'currency_symbol' => cleanInput($_POST['currency_symbol'] ?? 'FCFA'),
            'timezone' => cleanInput($_POST['timezone'] ?? 'Africa/Abidjan'),
            'registration_enabled' => isset($_POST['registration_enabled']) ? '1' : '0',
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0'
        ];
        
        $errors = [];
        if (empty($updates['site_name'])) $errors[] = 'Le nom du site est requis.';
        if (empty($updates['contact_email']) || !filter_var($updates['contact_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email de contact invalide.';
        }
        
        if (empty($errors)) {
            foreach ($updates as $key => $value) {
                $db->execute("
                    INSERT INTO settings (setting_key, setting_value) 
                    VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                ", [$key, $value]);
            }
            
            logAdminAction($user['id'], 'settings_general_update', 'Paramètres généraux mis à jour');
            setFlashMessage('success', 'Paramètres généraux mis à jour avec succès.');
        } else {
            setFlashMessage('error', implode('<br>', $errors));
        }
        redirect('/admin/settings.php');
    }
    
    if ($action === 'update_payment') {
        $paymentMethods = [
            'mtn_number' => cleanInput($_POST['mtn_number'] ?? ''),
            'moov_number' => cleanInput($_POST['moov_number'] ?? ''),
            'orange_number' => cleanInput($_POST['orange_number'] ?? ''),
            'payment_instructions' => cleanInput($_POST['payment_instructions'] ?? ''),
            'min_deposit' => floatval($_POST['min_deposit'] ?? 1000)
        ];
        
        foreach ($paymentMethods as $key => $value) {
            $db->execute("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ", [$key, $value]);
        }
        
        logAdminAction($user['id'], 'settings_payment_update', 'Paramètres de paiement mis à jour');
        setFlashMessage('success', 'Paramètres de paiement mis à jour avec succès.');
        redirect('/admin/settings.php');
    }
    
    if ($action === 'update_email') {
        $emailSettings = [
            'smtp_host' => cleanInput($_POST['smtp_host'] ?? ''),
            'smtp_port' => intval($_POST['smtp_port'] ?? 587),
            'smtp_username' => cleanInput($_POST['smtp_username'] ?? ''),
            'smtp_password' => cleanInput($_POST['smtp_password'] ?? ''),
            'smtp_encryption' => cleanInput($_POST['smtp_encryption'] ?? 'tls'),
            'email_from_name' => cleanInput($_POST['email_from_name'] ?? ''),
            'email_from_address' => cleanInput($_POST['email_from_address'] ?? '')
        ];
        
        foreach ($emailSettings as $key => $value) {
            $db->execute("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ", [$key, $value]);
        }
        
        logAdminAction($user['id'], 'settings_email_update', 'Paramètres email mis à jour');
        setFlashMessage('success', 'Paramètres email mis à jour avec succès.');
        redirect('/admin/settings.php');
    }
    
    if ($action === 'update_security') {
        $securitySettings = [
            'max_login_attempts' => intval($_POST['max_login_attempts'] ?? 5),
            'lockout_duration' => intval($_POST['lockout_duration'] ?? 300),
            'session_lifetime' => intval($_POST['session_lifetime'] ?? 3600),
            'password_min_length' => intval($_POST['password_min_length'] ?? 8),
            'enable_captcha' => isset($_POST['enable_captcha']) ? '1' : '0',
            'enable_two_factor' => isset($_POST['enable_two_factor']) ? '1' : '0'
        ];
        
        foreach ($securitySettings as $key => $value) {
            $db->execute("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ", [$key, $value]);
        }
        
        logAdminAction($user['id'], 'settings_security_update', 'Paramètres de sécurité mis à jour');
        setFlashMessage('success', 'Paramètres de sécurité mis à jour avec succès.');
        redirect('/admin/settings.php');
    }
}

// Récupérer les paramètres actuels
$currentSettings = getSettings();

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - <?= htmlspecialchars($siteName) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
            background: var(--bg-primary);
        }
        
        .admin-sidebar {
            width: 280px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }
        
        .admin-main {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }
        
        .settings-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .settings-tab {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .settings-tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .settings-tab:hover {
            color: var(--text-primary);
        }
        
        .settings-section {
            display: none;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 2rem;
        }
        
        .settings-section.active {
            display: block;
        }
        
        .section-title {
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        .settings-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .settings-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-group label {
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            background: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }
        
        .checkbox-group label {
            color: var(--text-primary);
            font-weight: 500;
            cursor: pointer;
        }
        
        .checkbox-group .description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .info-card {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .info-card .info-title {
            color: #3b82f6;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-card .info-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .warning-card {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .warning-card .warning-title {
            color: #f59e0b;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .warning-card .warning-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .settings-tabs {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body class="admin-page">
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fas fa-rocket"></i>
                    <span><?= htmlspecialchars($siteName) ?></span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Principal</div>
                    <a href="/admin/" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="/admin/orders.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Commandes</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Catalogue</div>
                    <a href="/admin/services.php" class="nav-link">
                        <i class="fas fa-cogs"></i>
                        <span>Services</span>
                    </a>
                    <a href="/admin/categories.php" class="nav-link">
                        <i class="fas fa-tags"></i>
                        <span>Catégories</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Utilisateurs</div>
                    <a href="/admin/users.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Clients</span>
                    </a>
                    <a href="/admin/messages.php" class="nav-link">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Configuration</div>
                    <a href="/admin/settings.php" class="nav-link active">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                    <a href="/admin/pages.php" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Pages</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Système</div>
                    <a href="/admin/logs.php" class="nav-link">
                        <i class="fas fa-list-alt"></i>
                        <span>Logs</span>
                    </a>
                    <a href="/" class="nav-link" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Voir le site</span>
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>
                    <i class="fas fa-cog"></i>
                    Paramètres du Site
                </h1>
                
                <div class="admin-user">
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                        <div class="user-role"><?= ucfirst($user['role']) ?></div>
                    </div>
                    <a href="/logout.php" class="btn btn-outline btn-sm">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </div>
            </div>
            
            <?php if ($flashMessage): ?>
                <div class="alert alert-<?= $flashMessage['type'] ?>">
                    <i class="fas fa-<?= $flashMessage['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= $flashMessage['message'] ?>
                </div>
            <?php endif; ?>
            
            <!-- Onglets -->
            <div class="settings-tabs">
                <button class="settings-tab active" onclick="showTab('general')">
                    <i class="fas fa-cog"></i>
                    Général
                </button>
                <button class="settings-tab" onclick="showTab('payment')">
                    <i class="fas fa-credit-card"></i>
                    Paiement
                </button>
                <button class="settings-tab" onclick="showTab('email')">
                    <i class="fas fa-envelope"></i>
                    Email
                </button>
                <button class="settings-tab" onclick="showTab('security')">
                    <i class="fas fa-shield-alt"></i>
                    Sécurité
                </button>
            </div>
            
            <!-- Section Général -->
            <div class="settings-section active" id="general-section">
                <h2 class="section-title">
                    <i class="fas fa-cog"></i>
                    Paramètres Généraux
                </h2>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="update_general">
                    
                    <div class="settings-grid">
                        <div class="settings-group">
                            <div class="form-group">
                                <label for="site_name">Nom du site *</label>
                                <input type="text" id="site_name" name="site_name" 
                                       value="<?= htmlspecialchars($currentSettings['site_name'] ?? 'MaickelSMM') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_email">Email de contact *</label>
                                <input type="email" id="contact_email" name="contact_email" 
                                       value="<?= htmlspecialchars($currentSettings['contact_email'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_phone">Téléphone de contact</label>
                                <input type="text" id="contact_phone" name="contact_phone" 
                                       value="<?= htmlspecialchars($currentSettings['contact_phone'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="whatsapp_number">Numéro WhatsApp</label>
                                <input type="text" id="whatsapp_number" name="whatsapp_number" 
                                       value="<?= htmlspecialchars($currentSettings['whatsapp_number'] ?? '') ?>"
                                       placeholder="+225XXXXXXXXX">
                            </div>
                        </div>
                        
                        <div class="settings-group">
                            <div class="form-group">
                                <label for="currency">Devise</label>
                                <select id="currency" name="currency">
                                    <option value="XOF" <?= ($currentSettings['currency'] ?? 'XOF') === 'XOF' ? 'selected' : '' ?>>Franc CFA (XOF)</option>
                                    <option value="EUR" <?= ($currentSettings['currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>Euro (EUR)</option>
                                    <option value="USD" <?= ($currentSettings['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>Dollar US (USD)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="currency_symbol">Symbole de devise</label>
                                <input type="text" id="currency_symbol" name="currency_symbol" 
                                       value="<?= htmlspecialchars($currentSettings['currency_symbol'] ?? 'FCFA') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="timezone">Fuseau horaire</label>
                                <select id="timezone" name="timezone">
                                    <option value="Africa/Abidjan" <?= ($currentSettings['timezone'] ?? 'Africa/Abidjan') === 'Africa/Abidjan' ? 'selected' : '' ?>>Africa/Abidjan</option>
                                    <option value="Europe/Paris" <?= ($currentSettings['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : '' ?>>Europe/Paris</option>
                                    <option value="America/New_York" <?= ($currentSettings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>America/New_York</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="settings-group full-width">
                            <div class="form-group">
                                <label for="site_description">Description du site</label>
                                <textarea id="site_description" name="site_description" rows="3"
                                          placeholder="Description courte de votre site SMM..."><?= htmlspecialchars($currentSettings['site_description'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="registration_enabled" name="registration_enabled" 
                                       <?= ($currentSettings['registration_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                                <div>
                                    <label for="registration_enabled">Autoriser les inscriptions</label>
                                    <div class="description">Permet aux nouveaux utilisateurs de créer un compte</div>
                                </div>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                       <?= ($currentSettings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <div>
                                    <label for="maintenance_mode">Mode maintenance</label>
                                    <div class="description">Active le mode maintenance pour le site public</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Section Paiement -->
            <div class="settings-section" id="payment-section">
                <h2 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Paramètres de Paiement
                </h2>
                
                <div class="info-card">
                    <div class="info-title">
                        <i class="fas fa-info-circle"></i>
                        Information
                    </div>
                    <div class="info-text">
                        Configurez les numéros de Mobile Money pour recevoir les paiements de vos clients.
                        Ces informations seront affichées lors du processus de commande.
                    </div>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="update_payment">
                    
                    <div class="settings-grid">
                        <div class="settings-group">
                            <div class="form-group">
                                <label for="mtn_number">Numéro MTN Money</label>
                                <input type="text" id="mtn_number" name="mtn_number" 
                                       value="<?= htmlspecialchars($currentSettings['mtn_number'] ?? '') ?>"
                                       placeholder="+225XXXXXXXXX">
                            </div>
                            
                            <div class="form-group">
                                <label for="moov_number">Numéro Moov Money</label>
                                <input type="text" id="moov_number" name="moov_number" 
                                       value="<?= htmlspecialchars($currentSettings['moov_number'] ?? '') ?>"
                                       placeholder="+225XXXXXXXXX">
                            </div>
                            
                            <div class="form-group">
                                <label for="orange_number">Numéro Orange Money</label>
                                <input type="text" id="orange_number" name="orange_number" 
                                       value="<?= htmlspecialchars($currentSettings['orange_number'] ?? '') ?>"
                                       placeholder="+225XXXXXXXXX">
                            </div>
                        </div>
                        
                        <div class="settings-group">
                            <div class="form-group">
                                <label for="min_deposit">Montant minimum (<?= htmlspecialchars($currentSettings['currency_symbol'] ?? 'FCFA') ?>)</label>
                                <input type="number" id="min_deposit" name="min_deposit" 
                                       value="<?= htmlspecialchars($currentSettings['min_deposit'] ?? '1000') ?>"
                                       min="100" step="100">
                            </div>
                        </div>
                        
                        <div class="settings-group full-width">
                            <div class="form-group">
                                <label for="payment_instructions">Instructions de paiement</label>
                                <textarea id="payment_instructions" name="payment_instructions" rows="4"
                                          placeholder="Instructions détaillées pour les clients..."><?= htmlspecialchars($currentSettings['payment_instructions'] ?? 'Envoyez le montant exact via Mobile Money puis uploadez la preuve de paiement.') ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Section Email -->
            <div class="settings-section" id="email-section">
                <h2 class="section-title">
                    <i class="fas fa-envelope"></i>
                    Paramètres Email
                </h2>
                
                <div class="warning-card">
                    <div class="warning-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Configuration SMTP
                    </div>
                    <div class="warning-text">
                        Pour que les emails fonctionnent correctement, vous devez configurer un serveur SMTP.
                        Contactez votre hébergeur ou utilisez un service comme Gmail, SendGrid, ou Mailgun.
                    </div>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="update_email">
                    
                    <div class="settings-grid">
                        <div class="settings-group">
                            <div class="form-group">
                                <label for="smtp_host">Serveur SMTP</label>
                                <input type="text" id="smtp_host" name="smtp_host" 
                                       value="<?= htmlspecialchars($currentSettings['smtp_host'] ?? '') ?>"
                                       placeholder="smtp.gmail.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="smtp_port">Port SMTP</label>
                                <input type="number" id="smtp_port" name="smtp_port" 
                                       value="<?= htmlspecialchars($currentSettings['smtp_port'] ?? '587') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="smtp_encryption">Chiffrement</label>
                                <select id="smtp_encryption" name="smtp_encryption">
                                    <option value="tls" <?= ($currentSettings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                    <option value="ssl" <?= ($currentSettings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value="none" <?= ($currentSettings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>Aucun</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="settings-group">
                            <div class="form-group">
                                <label for="smtp_username">Nom d'utilisateur SMTP</label>
                                <input type="text" id="smtp_username" name="smtp_username" 
                                       value="<?= htmlspecialchars($currentSettings['smtp_username'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="smtp_password">Mot de passe SMTP</label>
                                <input type="password" id="smtp_password" name="smtp_password" 
                                       value="<?= htmlspecialchars($currentSettings['smtp_password'] ?? '') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email_from_name">Nom de l'expéditeur</label>
                                <input type="text" id="email_from_name" name="email_from_name" 
                                       value="<?= htmlspecialchars($currentSettings['email_from_name'] ?? $siteName) ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email_from_address">Email de l'expéditeur</label>
                                <input type="email" id="email_from_address" name="email_from_address" 
                                       value="<?= htmlspecialchars($currentSettings['email_from_address'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Section Sécurité -->
            <div class="settings-section" id="security-section">
                <h2 class="section-title">
                    <i class="fas fa-shield-alt"></i>
                    Paramètres de Sécurité
                </h2>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="update_security">
                    
                    <div class="settings-grid">
                        <div class="settings-group">
                            <div class="form-group">
                                <label for="max_login_attempts">Tentatives de connexion max</label>
                                <input type="number" id="max_login_attempts" name="max_login_attempts" 
                                       value="<?= htmlspecialchars($currentSettings['max_login_attempts'] ?? '5') ?>"
                                       min="3" max="10">
                            </div>
                            
                            <div class="form-group">
                                <label for="lockout_duration">Durée de blocage (secondes)</label>
                                <input type="number" id="lockout_duration" name="lockout_duration" 
                                       value="<?= htmlspecialchars($currentSettings['lockout_duration'] ?? '300') ?>"
                                       min="60" step="60">
                            </div>
                            
                            <div class="form-group">
                                <label for="session_lifetime">Durée de session (secondes)</label>
                                <input type="number" id="session_lifetime" name="session_lifetime" 
                                       value="<?= htmlspecialchars($currentSettings['session_lifetime'] ?? '3600') ?>"
                                       min="300" step="300">
                            </div>
                        </div>
                        
                        <div class="settings-group">
                            <div class="form-group">
                                <label for="password_min_length">Longueur minimale mot de passe</label>
                                <input type="number" id="password_min_length" name="password_min_length" 
                                       value="<?= htmlspecialchars($currentSettings['password_min_length'] ?? '8') ?>"
                                       min="6" max="20">
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="enable_captcha" name="enable_captcha" 
                                       <?= ($currentSettings['enable_captcha'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <div>
                                    <label for="enable_captcha">Activer le CAPTCHA</label>
                                    <div class="description">Protection contre les bots sur les formulaires</div>
                                </div>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="enable_two_factor" name="enable_two_factor" 
                                       <?= ($currentSettings['enable_two_factor'] ?? '0') === '1' ? 'checked' : '' ?>>
                                <div>
                                    <label for="enable_two_factor">Authentification à deux facteurs</label>
                                    <div class="description">Sécurité renforcée pour les comptes admin</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
        function showTab(tabName) {
            // Masquer toutes les sections
            document.querySelectorAll('.settings-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Désactiver tous les onglets
            document.querySelectorAll('.settings-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Afficher la section sélectionnée
            document.getElementById(tabName + '-section').classList.add('active');
            
            // Activer l'onglet sélectionné
            event.target.classList.add('active');
        }
        
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