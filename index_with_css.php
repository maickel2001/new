<?php
// Version de test avec CSS intégré
try {
    require_once 'config/config.php';
    require_once 'includes/functions.php';
    $categories = getCategories(true);
    $services = getAllServices(true);
    $settings = getSettings();
} catch (Exception $e) {
    // Version simplifiée si les includes ne marchent pas
    $categories = [];
    $services = [];
    $settings = ['site_name' => 'MaickelSMM', 'site_description' => 'Services SMM de qualité'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $settings['site_name'] ?? 'MaickelSMM' ?> - Services SMM</title>
    <style>
        /* CSS INTÉGRÉ POUR TEST */
        :root {
            --bg-primary: #0f0f23;
            --bg-secondary: #16213e;
            --bg-card: #1a1a2e;
            --text-primary: #ffffff;
            --text-secondary: #b0b3c1;
            --accent-blue: #00d4ff;
            --accent-pink: #ff6b6b;
            --accent-purple: #a855f7;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --border-color: rgba(255, 255, 255, 0.1);
            --shadow: rgba(0, 0, 0, 0.3);
            --radius: 12px;
            --spacing: 1rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 0;
        }

        .header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            background: linear-gradient(45deg, var(--accent-blue), var(--accent-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-menu a:hover {
            color: var(--accent-blue);
        }

        /* Hero Section */
        .hero {
            padding: 120px 0 80px;
            text-align: center;
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
        }

        .hero .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, var(--accent-blue), var(--accent-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--accent-blue), var(--accent-pink));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 212, 255, 0.3);
        }

        /* Services Section */
        .services-section {
            padding: 80px 0;
            background: var(--bg-primary);
        }

        .services-section .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 3rem;
            background: linear-gradient(45deg, var(--accent-blue), var(--accent-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Categories Navigation */
        .categories-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 3rem;
        }

        .category-btn {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .category-btn:hover,
        .category-btn.active {
            background: linear-gradient(45deg, var(--accent-blue), var(--accent-pink));
            color: white;
            border-color: transparent;
        }

        /* Grid System */
        .grid {
            display: grid;
            gap: 2rem;
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        /* Service Cards */
        .service-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(45deg, var(--accent-blue), var(--accent-pink));
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-color: var(--accent-blue);
        }

        .service-title {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .service-description {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .service-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .service-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--accent-blue);
        }

        .service-delivery {
            color: var(--text-secondary);
        }

        /* Footer */
        .footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border-color);
            padding: 3rem 0;
            text-align: center;
        }

        .footer .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .nav-menu {
                display: none;
            }
            
            .grid-3 {
                grid-template-columns: 1fr;
            }
            
            .service-meta {
                grid-template-columns: 1fr;
            }
        }

        /* Flash Messages */
        .flash-messages {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1001;
        }

        .flash-message {
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            color: white;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        .flash-success {
            background: var(--success);
        }

        .flash-error {
            background: var(--error);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Flash Messages -->
    <div class="flash-messages" id="flash-messages"></div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo"><?= $settings['site_name'] ?? 'MaickelSMM' ?></div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="#services">Services</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="login.php">Connexion</a></li>
                    <li><a href="register.php">Inscription</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="hero-title"><?= $settings['site_name'] ?? 'MaickelSMM' ?></h1>
            <p class="hero-subtitle">
                <?= $settings['site_description'] ?? 'Boostez votre présence sur les réseaux sociaux avec nos services SMM de qualité professionnelle' ?>
            </p>
            <a href="#services" class="btn btn-primary">Découvrir nos services</a>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <h2 class="section-title">Nos Services SMM</h2>
            
            <!-- Categories Navigation -->
            <?php if (!empty($categories)): ?>
            <div class="categories-nav">
                <a href="#" class="category-btn active" data-category="all">Tous</a>
                <?php foreach ($categories as $category): ?>
                <a href="#" class="category-btn" data-category="<?= $category['id'] ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Services Grid -->
            <div class="grid grid-3">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $service): ?>
                    <div class="service-card" data-category="<?= $service['category_id'] ?>">
                        <h3 class="service-title"><?= htmlspecialchars($service['name']) ?></h3>
                        <p class="service-description"><?= htmlspecialchars($service['description']) ?></p>
                        <div class="service-meta">
                            <div class="service-price"><?= number_format($service['price_per_1000'], 0) ?> FCFA</div>
                            <div class="service-delivery">⏱️ <?= htmlspecialchars($service['delivery_time']) ?></div>
                        </div>
                        <div class="service-meta">
                            <div>Min: <?= number_format($service['min_quantity']) ?></div>
                            <div>Max: <?= number_format($service['max_quantity']) ?></div>
                        </div>
                        <button class="btn btn-primary" onclick="alert('Commande: <?= htmlspecialchars($service['name']) ?>')">
                            Commander
                        </button>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Services de démonstration si la DB ne marche pas -->
                    <div class="service-card">
                        <h3 class="service-title">Followers Instagram</h3>
                        <p class="service-description">Augmentez votre nombre de followers Instagram avec des comptes réels et actifs</p>
                        <div class="service-meta">
                            <div class="service-price">2,500 FCFA</div>
                            <div class="service-delivery">⏱️ 1-3 jours</div>
                        </div>
                        <div class="service-meta">
                            <div>Min: 100</div>
                            <div>Max: 10,000</div>
                        </div>
                        <button class="btn btn-primary">Commander</button>
                    </div>
                    
                    <div class="service-card">
                        <h3 class="service-title">Likes TikTok</h3>
                        <p class="service-description">Boostez vos vidéos TikTok avec des likes de qualité pour améliorer votre visibilité</p>
                        <div class="service-meta">
                            <div class="service-price">1,500 FCFA</div>
                            <div class="service-delivery">⏱️ 0-6 heures</div>
                        </div>
                        <div class="service-meta">
                            <div>Min: 50</div>
                            <div>Max: 50,000</div>
                        </div>
                        <button class="btn btn-primary">Commander</button>
                    </div>
                    
                    <div class="service-card">
                        <h3 class="service-title">Vues YouTube</h3>
                        <p class="service-description">Augmentez le nombre de vues sur vos vidéos YouTube pour améliorer leur classement</p>
                        <div class="service-meta">
                            <div class="service-price">3,000 FCFA</div>
                            <div class="service-delivery">⏱️ 12-48 heures</div>
                        </div>
                        <div class="service-meta">
                            <div>Min: 1,000</div>
                            <div>Max: 100,000</div>
                        </div>
                        <button class="btn btn-primary">Commander</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 <?= $settings['site_name'] ?? 'MaickelSMM' ?>. Tous droits réservés.</p>
            <p>Services SMM professionnels - Croissance garantie</p>
        </div>
    </footer>

    <script>
        // Simple category filtering
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update active button
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Filter services
                const category = this.dataset.category;
                document.querySelectorAll('.service-card').forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>