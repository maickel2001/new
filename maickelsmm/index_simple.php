<?php
/**
 * Page d'accueil simplifiée pour test - MaickelSMM
 */

// Inclusions de base
try {
    require_once 'config/config.php';
    require_once 'includes/functions.php';
    echo "<!-- Fichiers de configuration chargés -->\n";
} catch (Exception $e) {
    die("Erreur de configuration : " . htmlspecialchars($e->getMessage()));
}

// Charger les données
try {
    $categories = getCategories(true);
    $services = getAllServices(true);
    $settings = getSettings();
    echo "<!-- Données chargées : " . count($services) . " services, " . count($categories) . " catégories -->\n";
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
    <title><?= htmlspecialchars($siteName) ?> - Panneau SMM Professionnel</title>
    <meta name="description" content="<?= htmlspecialchars($siteDescription) ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header simplifié -->
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
                <h1 class="hero-title">Boostez votre présence sur les réseaux sociaux</h1>
                <p class="hero-subtitle">Services SMM professionnels pour Instagram, TikTok, YouTube, Facebook et plus encore</p>
                <a href="#services" class="btn btn-primary btn-lg">Découvrir nos services</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Nos Services SMM</h2>
                <p class="section-subtitle">Choisissez parmi nos <?= count($services) ?> services dans <?= count($categories) ?> catégories</p>
            </div>

            <!-- Categories Navigation -->
            <div class="categories-nav">
                <button class="category-btn active" data-category="all">
                    <i class="fas fa-th"></i> Tous
                </button>
                <?php foreach ($categories as $category): ?>
                <button class="category-btn" data-category="<?= $category['id'] ?>">
                    <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                    <?= htmlspecialchars($category['name']) ?>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Services Grid -->
            <div class="services-grid grid grid-3" id="services-container">
                <?php foreach ($services as $service): ?>
                <div class="card service-card" data-service-id="<?= $service['id'] ?>" data-category="<?= $service['category_id'] ?>">
                    <div class="card-header">
                        <h3 class="service-name"><?= htmlspecialchars($service['name']) ?></h3>
                        <span class="service-category"><?= htmlspecialchars($service['category_name']) ?></span>
                    </div>
                    <div class="card-body">
                        <p class="service-description"><?= htmlspecialchars(substr($service['description'], 0, 100)) ?>...</p>
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
                        <button class="btn btn-primary btn-sm" onclick="alert('Service: <?= htmlspecialchars($service['name']) ?>')">
                            Commander
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer simplifié -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= htmlspecialchars($siteName) ?></h3>
                    <p>Votre partenaire de confiance pour le marketing des réseaux sociaux.</p>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>Email: <?= htmlspecialchars($settings['contact_email'] ?? 'contact@maickelsmm.com') ?></p>
                    <p>Téléphone: <?= htmlspecialchars($settings['contact_phone'] ?? '+225 07 12 34 56 78') ?></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Script simplifié pour les catégories
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
        });
    </script>
</body>
</html>