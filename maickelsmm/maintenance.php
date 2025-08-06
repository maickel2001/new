<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Vérifier si le mode maintenance est activé
$settings = getSettings();
$maintenanceMode = ($settings['maintenance_mode'] ?? '0') === '1';
$maintenanceMessage = $settings['maintenance_message'] ?? 'Site en maintenance. Nous serons de retour bientôt.';
$siteName = $settings['site_name'] ?? 'MaickelSMM';

// Si la maintenance n'est pas activée, rediriger vers l'accueil
if (!$maintenanceMode) {
    redirect('/');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - <?= htmlspecialchars($siteName) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
    <style>
        .maintenance-page {
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        
        .maintenance-container {
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        
        .maintenance-icon {
            font-size: 6rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
        }
        
        .maintenance-title {
            font-size: 2.5rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-weight: bold;
        }
        
        .maintenance-message {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 3rem;
            line-height: 1.6;
        }
        
        .maintenance-info {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 3rem;
        }
        
        .maintenance-info h3 {
            color: var(--text-primary);
            margin: 0 0 1rem 0;
            font-size: 1.3rem;
        }
        
        .maintenance-info p {
            color: var(--text-secondary);
            margin: 0 0 1.5rem 0;
        }
        
        .maintenance-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            text-align: left;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .feature-content h4 {
            color: var(--text-primary);
            margin: 0 0 0.5rem 0;
            font-size: 1rem;
        }
        
        .feature-content p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .maintenance-contact {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .maintenance-contact h3 {
            color: var(--text-primary);
            margin: 0 0 1rem 0;
            font-size: 1.2rem;
        }
        
        .maintenance-contact p {
            color: var(--text-secondary);
            margin: 0 0 1rem 0;
        }
        
        .contact-methods {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .contact-method {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .contact-method:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background: var(--border-color);
            border-radius: 3px;
            overflow: hidden;
            margin: 1rem 0;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 3px;
            animation: progress 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes progress {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }
        
        .maintenance-footer {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .maintenance-title {
                font-size: 2rem;
            }
            
            .maintenance-icon {
                font-size: 4rem;
            }
            
            .maintenance-features {
                grid-template-columns: 1fr;
            }
            
            .contact-methods {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="maintenance-page">
    <div class="maintenance-container">
        <div class="maintenance-icon">
            <i class="fas fa-tools"></i>
        </div>
        
        <h1 class="maintenance-title">Site en maintenance</h1>
        
        <div class="maintenance-message">
            <?= htmlspecialchars($maintenanceMessage) ?>
        </div>
        
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
        
        <div class="maintenance-info">
            <h3>
                <i class="fas fa-rocket"></i>
                Nous améliorons <?= htmlspecialchars($siteName) ?>
            </h3>
            <p>Notre équipe travaille actuellement sur de nouvelles fonctionnalités pour améliorer votre expérience SMM.</p>
            
            <div class="maintenance-features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Sécurité renforcée</h4>
                        <p>Mise à jour des protocoles de sécurité</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Performances optimisées</h4>
                        <p>Amélioration de la vitesse du site</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Nouveaux services</h4>
                        <p>Ajout de services SMM innovants</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Interface mobile</h4>
                        <p>Expérience mobile améliorée</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="maintenance-contact">
            <h3>
                <i class="fas fa-headset"></i>
                Besoin d'aide urgente ?
            </h3>
            <p>Notre équipe support reste disponible pendant la maintenance.</p>
            
            <div class="contact-methods">
                <?php if (!empty($settings['contact_whatsapp'])): ?>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['contact_whatsapp']) ?>?text=Bonjour, j'ai besoin d'aide pendant la maintenance" 
                       class="contact-method" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        WhatsApp
                    </a>
                <?php endif; ?>
                
                <a href="mailto:<?= htmlspecialchars($settings['contact_email'] ?? 'contact@maickelsmm.com') ?>" 
                   class="contact-method">
                    <i class="fas fa-envelope"></i>
                    Email
                </a>
            </div>
        </div>
        
        <div class="maintenance-footer">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. Tous droits réservés.</p>
            <p>Merci pour votre patience !</p>
        </div>
    </div>

    <script>
        // Auto-refresh toutes les 5 minutes pour vérifier si la maintenance est terminée
        setTimeout(() => {
            window.location.reload();
        }, 300000); // 5 minutes
        
        // Afficher un message de rechargement automatique
        console.log('La page se rechargera automatiquement dans 5 minutes pour vérifier la fin de la maintenance.');
    </script>
</body>
</html>