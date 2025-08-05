<?php
/**
 * Page d'accueil - MaickelSMM
 * Affichage des services SMM par catégories
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Vérifier le mode maintenance
if (isMaintenanceMode() && !hasPermission(ROLE_ADMIN)) {
    include 'pages/maintenance.php';
    exit;
}

// Charger les données
$categories = getCategories();
$services = getAllServices();
$settings = getSettings();

// Grouper les services par catégorie
$servicesByCategory = [];
foreach ($services as $service) {
    $servicesByCategory[$service['category_id']][] = $service;
}

$pageTitle = $settings['home_title'] ?? 'MaickelSMM - Panneau SMM Professionnel';
$pageDescription = $settings['home_subtitle'] ?? 'Boostez votre présence sur les réseaux sociaux avec nos services de qualité';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="keywords" content="SMM, réseaux sociaux, followers, likes, vues, Instagram, TikTok, YouTube, Facebook">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= ASSETS_URL ?>/images/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Meta Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= BASE_URL ?>">
    <meta property="og:image" content="<?= ASSETS_URL ?>/images/og-image.jpg">
    
    <!-- Meta Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="twitter:image" content="<?= ASSETS_URL ?>/images/og-image.jpg">
</head>
<body>
    <!-- Messages Flash -->
    <div class="flash-messages">
        <?php foreach (getFlashMessages() as $message): ?>
            <div class="flash-message flash-<?= $message['type'] ?>">
                <i class="fas fa-<?= $message['type'] === 'success' ? 'check-circle' : ($message['type'] === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                <span><?= htmlspecialchars($message['message']) ?></span>
                <button class="flash-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="/" class="logo">
                    <i class="fas fa-rocket"></i>
                    <?= getSetting('site_name', 'MaickelSMM') ?>
                </a>
                
                <ul class="nav-menu">
                    <li><a href="#services" class="nav-link">Services</a></li>
                    <li><a href="#features" class="nav-link">Avantages</a></li>
                    <li><a href="/pages/faq.php" class="nav-link">FAQ</a></li>
                    <li><a href="/pages/contact.php" class="nav-link">Contact</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="/dashboard/" class="nav-link">
                            <i class="fas fa-user"></i> Dashboard
                        </a></li>
                        <li><a href="/logout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a></li>
                    <?php else: ?>
                        <li><a href="/login.php" class="nav-link">
                            <i class="fas fa-sign-in-alt"></i> Connexion
                        </a></li>
                        <li><a href="/register.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus"></i> S'inscrire
                        </a></li>
                    <?php endif; ?>
                </ul>
                
                <button class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1><?= htmlspecialchars($settings['home_title'] ?? 'MaickelSMM') ?></h1>
                <p><?= htmlspecialchars($settings['home_subtitle'] ?? 'Boostez votre présence sur les réseaux sociaux avec nos services de qualité premium, livraison rapide et prix compétitifs.') ?></p>
                
                <div class="hero-actions">
                    <a href="#services" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket"></i> Voir nos services
                    </a>
                    <a href="/pages/contact.php" class="btn btn-secondary btn-lg">
                        <i class="fab fa-whatsapp"></i> Nous contacter
                    </a>
                </div>
                
                <!-- Statistiques -->
                <div class="hero-stats">
                    <div class="stat">
                        <div class="stat-number"><?= count($services) ?>+</div>
                        <div class="stat-label">Services disponibles</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support client</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Sécurisé</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Pourquoi choisir MaickelSMM ?</h2>
                <p>Nous offrons les meilleurs services SMM avec une qualité premium et des prix compétitifs</p>
            </div>
            
            <div class="grid grid-4">
                <?php 
                $features = json_decode($settings['home_features'] ?? '[]', true);
                $defaultFeatures = [
                    ['icon' => 'fas fa-shield-alt', 'title' => 'Services de qualité premium', 'desc' => 'Tous nos services sont de haute qualité avec des profils réels et actifs'],
                    ['icon' => 'fas fa-clock', 'title' => 'Livraison rapide et fiable', 'desc' => 'Commencez à voir les résultats en quelques minutes à quelques heures'],
                    ['icon' => 'fas fa-headset', 'title' => 'Support client 24/7', 'desc' => 'Notre équipe est disponible 24h/24 pour répondre à vos questions'],
                    ['icon' => 'fas fa-tag', 'title' => 'Prix compétitifs', 'desc' => 'Les meilleurs prix du marché sans compromis sur la qualité']
                ];
                
                foreach ($defaultFeatures as $feature):
                ?>
                <div class="card feature-card slide-up">
                    <div class="feature-icon">
                        <i class="<?= $feature['icon'] ?>"></i>
                    </div>
                    <h3><?= $feature['title'] ?></h3>
                    <p><?= $feature['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <div class="section-title">
                <h2>Nos Services SMM</h2>
                <p>Choisissez parmi notre large gamme de services pour booster votre présence sur les réseaux sociaux</p>
            </div>
            
            <!-- Barre de recherche -->
            <div class="search-bar">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="service-search" class="form-control" placeholder="Rechercher un service...">
                </div>
            </div>
            
            <!-- Navigation des catégories -->
            <div class="categories-nav">
                <button class="category-btn active" data-category="all">
                    <i class="fas fa-globe"></i> Tous les services
                </button>
                <?php foreach ($categories as $category): ?>
                <button class="category-btn" data-category="<?= $category['id'] ?>">
                    <i class="<?= $category['icon'] ?>"></i> <?= htmlspecialchars($category['name']) ?>
                </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Grille des services -->
            <div class="services-grid grid grid-3">
                <?php foreach ($services as $service): ?>
                <div class="card service-card slide-up" data-service-id="<?= $service['id'] ?>" data-category="<?= $service['category_id'] ?>">
                    <div class="service-icon">
                        <i class="<?= $service['category_icon'] ?>"></i>
                    </div>
                    <h3 class="service-name"><?= htmlspecialchars($service['name']) ?></h3>
                    <p class="service-description"><?= htmlspecialchars(truncateText($service['description'], 100)) ?></p>
                    <div class="service-price"><?= formatPrice($service['price_per_1000']) ?> / 1000</div>
                    <div class="service-details">
                        <span><i class="fas fa-arrow-down"></i> Min: <?= number_format($service['min_quantity']) ?></span>
                        <span><i class="fas fa-arrow-up"></i> Max: <?= number_format($service['max_quantity']) ?></span>
                        <span><i class="fas fa-clock"></i> <?= htmlspecialchars($service['delivery_time']) ?></span>
                    </div>
                    <?php if ($service['guarantee'] === 'yes'): ?>
                    <div class="service-guarantee">
                        <i class="fas fa-shield-alt"></i> Garantie
                    </div>
                    <?php endif; ?>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-sm" onclick="openOrderModal(<?= $service['id'] ?>)">
                            <i class="fas fa-shopping-cart"></i> Commander
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="showServiceDetails(<?= $service['id'] ?>)">
                            <i class="fas fa-info-circle"></i> Détails
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Message si aucun service -->
            <div id="no-services" class="text-center" style="display: none;">
                <i class="fas fa-search text-6xl text-gray-400 mb-4"></i>
                <h3>Aucun service trouvé</h3>
                <p>Essayez de modifier vos critères de recherche ou de sélectionner une autre catégorie.</p>
            </div>
        </div>
    </section>

    <!-- How it works Section -->
    <section class="how-it-works-section">
        <div class="container">
            <div class="section-title">
                <h2>Comment ça marche ?</h2>
                <p>Commandez vos services SMM en 4 étapes simples</p>
            </div>
            
            <div class="grid grid-4">
                <div class="step-card slide-up">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <h3>Choisissez un service</h3>
                    <p>Parcourez notre catalogue et sélectionnez le service qui correspond à vos besoins</p>
                </div>
                
                <div class="step-card slide-up">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3>Remplissez le formulaire</h3>
                    <p>Indiquez la quantité désirée et le lien de votre profil ou publication</p>
                </div>
                
                <div class="step-card slide-up">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>Effectuez le paiement</h3>
                    <p>Payez via Mobile Money et uploadez la preuve de paiement</p>
                </div>
                
                <div class="step-card slide-up">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Profitez des résultats</h3>
                    <p>Votre commande est traitée et vous commencez à voir les résultats rapidement</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Prêt à booster votre présence sur les réseaux sociaux ?</h2>
                <p>Rejoignez des milliers de clients satisfaits qui font confiance à MaickelSMM pour leurs besoins en marketing digital</p>
                <div class="cta-actions">
                    <a href="#services" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-cart"></i> Commencer maintenant
                    </a>
                    <a href="https://wa.me/<?= str_replace(['+', ' '], '', getSetting('contact_whatsapp', '')) ?>" class="btn btn-secondary btn-lg" target="_blank">
                        <i class="fab fa-whatsapp"></i> Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= getSetting('site_name', 'MaickelSMM') ?></h3>
                    <p>Votre partenaire de confiance pour tous vos besoins en marketing des réseaux sociaux. Services de qualité premium, livraison rapide et support 24/7.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Services</h3>
                    <ul>
                        <li><a href="#services">Instagram</a></li>
                        <li><a href="#services">TikTok</a></li>
                        <li><a href="#services">YouTube</a></li>
                        <li><a href="#services">Facebook</a></li>
                        <li><a href="#services">Twitter</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul>
                        <li><a href="/pages/faq.php">FAQ</a></li>
                        <li><a href="/pages/contact.php">Contact</a></li>
                        <li><a href="/pages/terms.php">Conditions d'utilisation</a></li>
                        <li><a href="/pages/privacy.php">Politique de confidentialité</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Contact</h3>
                    <ul>
                        <li><i class="fas fa-envelope"></i> <?= getSetting('contact_email', 'contact@maickelsmm.com') ?></li>
                        <li><i class="fas fa-phone"></i> <?= getSetting('contact_phone', '+225 07 12 34 56 78') ?></li>
                        <li><i class="fab fa-whatsapp"></i> <?= getSetting('contact_whatsapp', '+225 07 12 34 56') ?></li>
                        <li><i class="fas fa-clock"></i> 24h/24 - 7j/7</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= getSetting('site_name', 'MaickelSMM') ?>. Tous droits réservés.</p>
                <p>Développé avec ❤️ pour booster votre succès sur les réseaux sociaux</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?= ASSETS_URL ?>/js/main.js"></script>
    
    <!-- Données pour JavaScript -->
    <script>
        // Configuration globale
        window.MAICKEL_CONFIG = {
            baseUrl: '<?= BASE_URL ?>',
            apiUrl: '<?= BASE_URL ?>/api',
            assetsUrl: '<?= ASSETS_URL ?>',
            isLoggedIn: <?= isLoggedIn() ? 'true' : 'false' ?>,
            currency: '<?= getSetting('currency_symbol', 'FCFA') ?>',
            services: <?= json_encode($services) ?>,
            categories: <?= json_encode($categories) ?>,
            paymentMethods: <?= json_encode(getPaymentMethods()) ?>
        };
    </script>

    <!-- CSS additionnels pour les sections spécifiques -->
    <style>
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: var(--spacing-2xl);
            margin-top: var(--spacing-2xl);
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: var(--spacing-xs);
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        
        .search-bar {
            max-width: 500px;
            margin: 0 auto var(--spacing-xl);
        }
        
        .search-input-wrapper {
            position: relative;
        }
        
        .search-input-wrapper i {
            position: absolute;
            left: var(--spacing-md);
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        
        .search-input-wrapper .form-control {
            padding-left: 3rem;
        }
        
        .feature-card {
            text-align: center;
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: var(--spacing-lg);
        }
        
        .step-card {
            text-align: center;
            position: relative;
            padding-top: var(--spacing-2xl);
        }
        
        .step-number {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .step-icon {
            font-size: 2.5rem;
            color: var(--accent-color);
            margin: var(--spacing-lg) 0;
        }
        
        .cta-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            text-align: center;
            padding: var(--spacing-2xl) 0;
        }
        
        .cta-content h2 {
            font-size: 2.5rem;
            margin-bottom: var(--spacing-lg);
        }
        
        .cta-actions {
            display: flex;
            justify-content: center;
            gap: var(--spacing-lg);
            margin-top: var(--spacing-xl);
        }
        
        .social-links {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-md);
        }
        
        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--bg-tertiary);
            border-radius: 50%;
            color: var(--text-secondary);
            transition: var(--transition);
        }
        
        .social-link:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .hero-stats {
                flex-direction: column;
                gap: var(--spacing-lg);
            }
            
            .cta-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .cta-content h2 {
                font-size: 2rem;
            }
        }
    </style>
</body>
</html>