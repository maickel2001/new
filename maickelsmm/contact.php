<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';

$settings = getSettings();
$siteName = $settings['site_name'] ?? 'MaickelSMM';
$contactWhatsapp = $settings['contact_whatsapp'] ?? '';
$contactEmail = $settings['contact_email'] ?? 'contact@maickelsmm.com';

$error = '';
$success = '';

// Traitement du formulaire de contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Token de sécurité invalide.';
    } else {
        $data = [
            'name' => cleanInput($_POST['name'] ?? ''),
            'email' => cleanInput($_POST['email'] ?? ''),
            'subject' => cleanInput($_POST['subject'] ?? ''),
            'message' => cleanInput($_POST['message'] ?? ''),
            'phone' => cleanInput($_POST['phone'] ?? '')
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['name']) || strlen($data['name']) < 2) {
            $errors[] = 'Le nom doit contenir au moins 2 caractères.';
        }
        
        if (!isValidEmail($data['email'])) {
            $errors[] = 'Veuillez saisir un email valide.';
        }
        
        if (empty($data['subject']) || strlen($data['subject']) < 5) {
            $errors[] = 'Le sujet doit contenir au moins 5 caractères.';
        }
        
        if (empty($data['message']) || strlen($data['message']) < 10) {
            $errors[] = 'Le message doit contenir au moins 10 caractères.';
        }
        
        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        } else {
            // Sauvegarder le message de contact
            $db = Database::getInstance();
            $saved = $db->execute("
                INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ", [
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['subject'],
                $data['message'],
                getUserIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            if ($saved) {
                // Envoyer un email de notification (optionnel)
                $emailSubject = "Nouveau message de contact - " . $data['subject'];
                $emailMessage = "
                    Nouveau message reçu sur le site {$siteName}\n\n
                    Nom: {$data['name']}\n
                    Email: {$data['email']}\n
                    Téléphone: {$data['phone']}\n
                    Sujet: {$data['subject']}\n\n
                    Message:\n{$data['message']}\n\n
                    IP: " . getUserIP() . "\n
                    Date: " . date('Y-m-d H:i:s')
                ;
                
                sendEmail($contactEmail, $emailSubject, $emailMessage);
                
                $success = 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.';
                
                // Réinitialiser le formulaire
                $_POST = [];
            } else {
                $error = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
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
    <title>Contact - <?= htmlspecialchars($siteName) ?></title>
    <meta name="description" content="Contactez l'équipe <?= htmlspecialchars($siteName) ?> pour toute question sur nos services SMM. Support client disponible par WhatsApp et email.">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-rocket"></i>
                <span><?= htmlspecialchars($siteName) ?></span>
            </div>
            
            <nav class="nav-menu">
                <a href="/" class="nav-link">Accueil</a>
                <a href="/#services" class="nav-link">Services</a>
                <a href="/contact.php" class="nav-link active">Contact</a>
                <?php if ($auth->isLoggedIn()): ?>
                    <a href="/dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Tableau de bord
                    </a>
                <?php else: ?>
                    <a href="/login.php" class="nav-link">Connexion</a>
                    <a href="/register.php" class="btn btn-primary btn-sm">S'inscrire</a>
                <?php endif; ?>
            </nav>

            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Contact Hero -->
    <section class="contact-hero">
        <div class="container">
            <div class="hero-content">
                <h1>
                    <i class="fas fa-headset"></i>
                    Contactez-nous
                </h1>
                <p>Notre équipe est là pour répondre à toutes vos questions sur nos services SMM</p>
            </div>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="contact-section">
        <div class="container">
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

            <div class="contact-grid">
                <!-- Informations de contact -->
                <div class="contact-info">
                    <h2>
                        <i class="fas fa-info-circle"></i>
                        Nos coordonnées
                    </h2>
                    
                    <div class="contact-methods">
                        <?php if ($contactWhatsapp): ?>
                            <div class="contact-method">
                                <div class="method-icon">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div class="method-content">
                                    <h3>WhatsApp</h3>
                                    <p>Support instantané disponible</p>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $contactWhatsapp) ?>?text=Bonjour, j'ai une question concernant vos services SMM" 
                                       class="btn btn-success" target="_blank">
                                        <i class="fab fa-whatsapp"></i>
                                        Discuter maintenant
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="method-content">
                                <h3>Email</h3>
                                <p>Réponse sous 24h maximum</p>
                                <a href="mailto:<?= htmlspecialchars($contactEmail) ?>" class="contact-link">
                                    <?= htmlspecialchars($contactEmail) ?>
                                </a>
                            </div>
                        </div>

                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="method-content">
                                <h3>Horaires</h3>
                                <p>Support disponible 7j/7</p>
                                <span class="hours">24h/24 - 7j/7</span>
                            </div>
                        </div>

                        <div class="contact-method">
                            <div class="method-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="method-content">
                                <h3>Réponse rapide</h3>
                                <p>Temps de réponse moyen</p>
                                <span class="response-time">< 2 heures</span>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ rapide -->
                    <div class="quick-faq">
                        <h3>
                            <i class="fas fa-question-circle"></i>
                            Questions fréquentes
                        </h3>
                        <div class="faq-list">
                            <div class="faq-item">
                                <strong>Combien de temps pour traiter ma commande ?</strong>
                                <p>La plupart des commandes sont traitées en 24-48h maximum.</p>
                            </div>
                            <div class="faq-item">
                                <strong>Comment effectuer le paiement ?</strong>
                                <p>Nous acceptons MTN Money, Moov Money et Orange Money.</p>
                            </div>
                            <div class="faq-item">
                                <strong>Mes données sont-elles sécurisées ?</strong>
                                <p>Oui, nous utilisons un chiffrement SSL et ne stockons aucune donnée sensible.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de contact -->
                <div class="contact-form-container">
                    <h2>
                        <i class="fas fa-paper-plane"></i>
                        Envoyez-nous un message
                    </h2>
                    
                    <form method="POST" class="contact-form">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">
                                    <i class="fas fa-user"></i>
                                    Nom complet *
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    required 
                                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                                    placeholder="Votre nom et prénom"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i>
                                    Email *
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
                        </div>

                        <div class="form-row">
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
                            
                            <div class="form-group">
                                <label for="subject">
                                    <i class="fas fa-tag"></i>
                                    Sujet *
                                </label>
                                <select id="subject" name="subject" required>
                                    <option value="">Choisissez un sujet</option>
                                    <option value="Question générale" <?= ($_POST['subject'] ?? '') === 'Question générale' ? 'selected' : '' ?>>Question générale</option>
                                    <option value="Problème de commande" <?= ($_POST['subject'] ?? '') === 'Problème de commande' ? 'selected' : '' ?>>Problème de commande</option>
                                    <option value="Problème de paiement" <?= ($_POST['subject'] ?? '') === 'Problème de paiement' ? 'selected' : '' ?>>Problème de paiement</option>
                                    <option value="Nouveau service" <?= ($_POST['subject'] ?? '') === 'Nouveau service' ? 'selected' : '' ?>>Demande de nouveau service</option>
                                    <option value="Réclamation" <?= ($_POST['subject'] ?? '') === 'Réclamation' ? 'selected' : '' ?>>Réclamation</option>
                                    <option value="Partenariat" <?= ($_POST['subject'] ?? '') === 'Partenariat' ? 'selected' : '' ?>>Proposition de partenariat</option>
                                    <option value="Autre" <?= ($_POST['subject'] ?? '') === 'Autre' ? 'selected' : '' ?>>Autre</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message">
                                <i class="fas fa-comment"></i>
                                Message *
                            </label>
                            <textarea 
                                id="message" 
                                name="message" 
                                required 
                                rows="6"
                                placeholder="Décrivez votre demande en détail..."
                            ><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                            <div class="char-counter">
                                <span id="char-count">0</span> / 1000 caractères
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-full">
                                <i class="fas fa-paper-plane"></i>
                                Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-brand">
                        <i class="fas fa-rocket"></i>
                        <span><?= htmlspecialchars($siteName) ?></span>
                    </div>
                    <p>Votre partenaire de confiance pour tous vos besoins en marketing sur les réseaux sociaux.</p>
                </div>
                
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="/">Accueil</a></li>
                        <li><a href="/#services">Services</a></li>
                        <li><a href="/contact.php">Contact</a></li>
                        <li><a href="/terms.php">Conditions</a></li>
                        <li><a href="/privacy.php">Confidentialité</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="/contact.php">Centre d'aide</a></li>
                        <?php if ($contactWhatsapp): ?>
                            <li><a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $contactWhatsapp) ?>" target="_blank">WhatsApp</a></li>
                        <?php endif; ?>
                        <li><a href="mailto:<?= htmlspecialchars($contactEmail) ?>">Email</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Compteur de caractères pour le message
        const messageTextarea = document.getElementById('message');
        const charCount = document.getElementById('char-count');
        
        function updateCharCount() {
            const count = messageTextarea.value.length;
            charCount.textContent = count;
            
            if (count > 1000) {
                charCount.style.color = '#ff4444';
                messageTextarea.value = messageTextarea.value.substring(0, 1000);
                charCount.textContent = '1000';
            } else if (count > 800) {
                charCount.style.color = '#ff8800';
            } else {
                charCount.style.color = '#64748b';
            }
        }
        
        messageTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count

        // Auto-hide flash messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 8000);

        // Mobile menu toggle
        document.querySelector('.mobile-menu-toggle')?.addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('active');
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>