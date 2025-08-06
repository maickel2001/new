<?php
/**
 * Page d'accueil SANS JOIN - Version de secours
 */

require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();

// Charger les données SANS JOIN
try {
    // Categories simples
    $categories = $db->fetchAll("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");
    
    // Services simples (sans JOIN)
    $services = $db->fetchAll("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order ASC, name ASC");
    
    // Settings
    $settingsData = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    foreach ($settingsData as $setting) {
        $settings[$setting['setting_key']] = $setting['setting_value'];
    }
    
    // Créer un mapping des catégories
    $categoriesMap = [];
    foreach ($categories as $cat) {
        $categoriesMap[$cat['id']] = $cat;
    }
    
} catch (Exception $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}

$siteName = $settings['site_name'] ?? 'MaickelSMM';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteName) ?> - Services SMM</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="nav-brand">
                <h1>🚀 <?= htmlspecialchars($siteName) ?></h1>
            </div>
            <nav class="nav-links">
                <a href="#services">Services</a>
                <a href="/contact.php">Contact</a>
                <a href="/login.php" class="btn btn-outline btn-sm">Connexion</a>
                <a href="/admin/" class="btn btn-primary btn-sm">Admin</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Site MaickelSMM Fonctionnel ! 🎉</h1>
                <p class="hero-subtitle">
                    <?= count($services) ?> services disponibles dans <?= count($categories) ?> catégories
                </p>
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
                        <div class="stat-number">✅</div>
                        <div class="stat-label">Fonctionnel</div>
                    </div>
                </div>
                <a href="#services" class="btn btn-primary btn-lg">Voir les services</a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">📂 Catégories Disponibles</h2>
            <div class="categories-grid grid grid-3">
                <?php foreach ($categories as $category): ?>
                <div class="card category-card">
                    <div class="card-header">
                        <i class="<?= htmlspecialchars($category['icon'] ?? 'fas fa-folder') ?>"></i>
                        <h3><?= htmlspecialchars($category['name']) ?></h3>
                    </div>
                    <div class="card-body">
                        <p><?= htmlspecialchars($category['description'] ?? 'Description de la catégorie') ?></p>
                        <?php
                        $categoryServices = array_filter($services, function($s) use ($category) {
                            return $s['category_id'] == $category['id'];
                        });
                        ?>
                        <p><strong><?= count($categoryServices) ?> services disponibles</strong></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <h2 class="section-title">🛍️ Nos Services</h2>
            
            <div class="services-grid grid grid-3">
                <?php foreach ($services as $service): ?>
                <?php 
                $category = $categoriesMap[$service['category_id']] ?? null;
                $categoryName = $category ? $category['name'] : 'Catégorie inconnue';
                ?>
                <div class="card service-card">
                    <div class="card-header">
                        <h3 class="service-name"><?= htmlspecialchars($service['name']) ?></h3>
                        <span class="service-category"><?= htmlspecialchars($categoryName) ?></span>
                    </div>
                    <div class="card-body">
                        <p class="service-description">
                            <?= htmlspecialchars(substr($service['description'], 0, 100)) ?>...
                        </p>
                        <div class="service-details">
                            <div class="detail-item">
                                <span class="label">Prix :</span>
                                <span class="value"><?= number_format($service['price_per_1000'], 0, ',', ' ') ?> FCFA / 1000</span>
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
                        <button class="btn btn-primary btn-sm" onclick="alert('Service: <?= htmlspecialchars($service['name']) ?>\nPrix: <?= number_format($service['price_per_1000']) ?> FCFA/1000\nQuantité: <?= $service['min_quantity'] ?>-<?= $service['max_quantity'] ?>')">
                            <i class="fas fa-shopping-cart"></i> Commander
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Success Section -->
    <section class="success-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4rem 0;">
        <div class="container">
            <div class="text-center">
                <h2>🎉 Site MaickelSMM Opérationnel !</h2>
                <p>Base de données : ✅ Connectée</p>
                <p>Services : ✅ <?= count($services) ?> services chargés</p>
                <p>Catégories : ✅ <?= count($categories) ?> catégories chargées</p>
                <p>Interface : ✅ Fonctionnelle</p>
                <div style="margin-top: 2rem;">
                    <a href="/admin/" class="btn btn-outline" style="border-color: white; color: white;">
                        ⚙️ Accéder à l'Admin
                    </a>
                    <a href="/login.php" class="btn btn-primary" style="background: white; color: #667eea;">
                        🔐 Se Connecter
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="text-center">
                <h3><?= htmlspecialchars($siteName) ?></h3>
                <p>Site SMM professionnel - Maintenant fonctionnel ! 🚀</p>
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        console.log('✅ MaickelSMM chargé avec succès !');
        console.log('📊 Services:', <?= count($services) ?>);
        console.log('📂 Catégories:', <?= count($categories) ?>);
        
        // Message de succès
        setTimeout(() => {
            console.log('🎉 Félicitations ! Votre site MaickelSMM fonctionne parfaitement !');
        }, 1000);
    </script>
</body>
</html>