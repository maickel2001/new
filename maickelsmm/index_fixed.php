<?php
/**
 * Page d'accueil CORRIGÉE - MaickelSMM
 * Version qui fonctionne avec la vraie structure de la base
 */

// Inclusions de base
try {
    require_once 'config/config.php';
    require_once 'includes/functions.php';
} catch (Exception $e) {
    die("Erreur de configuration : " . htmlspecialchars($e->getMessage()));
}

// Charger les données avec les fonctions corrigées
try {
    $categories = getCategories(true); // Maintenant utilise is_active = 1
    $services = getAllServices(true);   // Maintenant utilise s.status = 'active' AND c.is_active = 1
    $settings = getSettings();
} catch (Exception $e) {
    die("Erreur de données : " . htmlspecialchars($e->getMessage()));
}

$siteName = $settings['site_name'] ?? 'MaickelSMM';
$siteDescription = $settings['site_description'] ?? 'Panneau SMM professionnel';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteName) ?> - Services SMM Professionnels</title>
    <meta name="description" content="<?= htmlspecialchars($siteDescription) ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="nav-brand">
                <h1><?= htmlspecialchars($siteName) ?></h1>
            </div>
            <nav class="nav-links">
                <a href="#services">Services</a>
                <a href="/contact.php">Contact</a>
                <a href="/login.php" class="btn btn-outline btn-sm">Connexion</a>
                <a href="/register.php" class="btn btn-primary btn-sm">Inscription</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">🚀 <?= htmlspecialchars($siteName) ?></h1>
                <p class="hero-subtitle">Services SMM professionnels - <?= count($services) ?> services dans <?= count($categories) ?> catégories</p>
                <div class="hero-stats">
                    <div class="stat">
                        <div class="stat-number"><?= count($services) ?></div>
                        <div class="stat-label">Services</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><?= count($categories) ?></div>
                        <div class="stat-label">Catégories</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support</div>
                    </div>
                </div>
                <a href="#services" class="btn btn-primary btn-lg">Découvrir nos services</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Nos Services SMM</h2>
                <p class="section-subtitle">Des services de qualité pour booster votre présence en ligne</p>
            </div>

            <!-- Categories Navigation -->
            <?php if (count($categories) > 0): ?>
            <div class="categories-nav">
                <button class="category-btn active" data-category="all">
                    <i class="fas fa-th"></i> Tous les services
                </button>
                <?php foreach ($categories as $category): ?>
                <button class="category-btn" data-category="<?= $category['id'] ?>">
                    <i class="<?= htmlspecialchars($category['icon'] ?? 'fas fa-folder') ?>"></i>
                    <?= htmlspecialchars($category['name']) ?>
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Services Grid -->
            <div class="services-grid grid grid-3" id="services-container">
                <?php if (count($services) > 0): ?>
                    <?php foreach ($services as $service): ?>
                    <div class="card service-card" data-service-id="<?= $service['id'] ?>" data-category="<?= $service['category_id'] ?>">
                        <div class="card-header">
                            <h3 class="service-name"><?= htmlspecialchars($service['name']) ?></h3>
                            <span class="service-category">
                                <?= htmlspecialchars($service['category_name'] ?? 'Catégorie') ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="service-description">
                                <?= htmlspecialchars(substr($service['description'], 0, 120)) ?>...
                            </p>
                            <div class="service-details">
                                <div class="detail-item">
                                    <span class="label">Prix :</span>
                                    <span class="value"><?= formatPrice($service['price_per_1000']) ?> / 1000</span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Quantité :</span>
                                    <span class="value"><?= number_format($service['min_quantity']) ?> - <?= number_format($service['max_quantity']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Livraison :</span>
                                    <span class="value"><?= htmlspecialchars($service['delivery_time']) ?></span>
                                </div>
                                <?php if ($service['guarantee'] === 'yes'): ?>
                                <div class="detail-item">
                                    <span class="guarantee-badge">✓ Garantie</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary btn-sm" onclick="alert('Service: <?= htmlspecialchars($service['name']) ?>\nPrix: <?= formatPrice($service['price_per_1000']) ?> FCFA/1000\nQuantité: <?= $service['min_quantity'] ?>-<?= $service['max_quantity'] ?>')">
                                <i class="fas fa-shopping-cart"></i> Commander
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-services">
                        <h3>Aucun service disponible</h3>
                        <p>Les services seront bientôt disponibles.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Pourquoi nous choisir ?</h2>
            </div>
            <div class="features-grid grid grid-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Livraison Rapide</h3>
                    <p>Services livrés dans les délais promis</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>100% Sécurisé</h3>
                    <p>Transactions et données protégées</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Support 24/7</h3>
                    <p>Assistance disponible à tout moment</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3>Qualité Premium</h3>
                    <p>Services de haute qualité garantis</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= htmlspecialchars($siteName) ?></h3>
                    <p>Votre partenaire de confiance pour le marketing des réseaux sociaux.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="/">Accueil</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="/contact.php">Contact</a></li>
                        <li><a href="/terms.php">CGU</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($settings['contact_email'] ?? 'contact@maickelsmm.com') ?></p>
                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($settings['contact_phone'] ?? '+225 07 12 34 56 78') ?></p>
                    <p><i class="fab fa-whatsapp"></i> WhatsApp disponible</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Script pour les catégories
        document.addEventListener('DOMContentLoaded', function() {
            const categoryBtns = document.querySelectorAll('.category-btn');
            const serviceCards = document.querySelectorAll('.service-card');

            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const category = this.dataset.category;
                    
                    // Update active button
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter services
                    serviceCards.forEach(card => {
                        if (category === 'all' || card.dataset.category === category) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
            
            console.log('✅ MaickelSMM chargé avec succès !');
            console.log('📊 Services:', <?= count($services) ?>);
            console.log('📂 Catégories:', <?= count($categories) ?>);
        });
    </script>
</body>
</html>