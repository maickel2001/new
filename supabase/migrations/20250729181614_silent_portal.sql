-- Base de données CREE 2GK
-- Création des tables principales

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    gradient VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des produits
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    image VARCHAR(255),
    badge VARCHAR(50),
    badge_color VARCHAR(50),
    delivery_time VARCHAR(100),
    platform VARCHAR(100),
    brand VARCHAR(100),
    rating DECIMAL(3,2) DEFAULT 0,
    reviews_count INT DEFAULT 0,
    stock_status ENUM('in_stock', 'out_of_stock', 'limited') DEFAULT 'in_stock',
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    features JSON,
    specifications JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_id VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des articles de commande
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    digital_code TEXT,
    code_status ENUM('pending', 'delivered', 'used') DEFAULT 'pending',
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table des codes numériques
CREATE TABLE IF NOT EXISTS digital_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    code VARCHAR(500) NOT NULL,
    status ENUM('available', 'sold', 'reserved') DEFAULT 'available',
    order_item_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sold_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE SET NULL
);

-- Table des avis
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255),
    comment TEXT,
    is_verified BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des favoris
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, product_id)
);

-- Table du panier
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(255),
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table des coupons
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    minimum_amount DECIMAL(10,2) DEFAULT 0,
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    expires_at DATETIME,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des logs d'activité
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des paramètres du site
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertion des catégories par défaut
INSERT INTO categories (name, slug, description, icon, gradient) VALUES
('Cartes Gaming', 'cartes-gaming', 'Steam, Epic Games, PlayStation, Xbox', 'ri-gamepad-line', 'from-purple-500 to-blue-500'),
('Streaming & Divertissement', 'streaming', 'Netflix, Spotify, YouTube Premium', 'ri-play-circle-line', 'from-red-500 to-pink-500'),
('Logiciels & Outils', 'logiciels', 'Windows, Office, Adobe, Antivirus', 'ri-computer-line', 'from-green-500 to-blue-500'),
('Cartes Prépayées', 'cartes-prepayees', 'Amazon, iTunes, Google Play', 'ri-gift-2-line', 'from-orange-500 to-yellow-500'),
('Cryptomonnaies', 'crypto', 'Bitcoin, Ethereum, codes crypto', 'ri-bit-coin-line', 'from-yellow-500 to-orange-500'),
('VPN & Sécurité', 'vpn-securite', 'NordVPN, ExpressVPN, antivirus', 'ri-shield-check-line', 'from-indigo-500 to-purple-500');

-- Insertion des produits d'exemple
INSERT INTO products (category_id, name, slug, description, short_description, price, original_price, image, badge, badge_color, delivery_time, platform, rating, stock_status, is_featured, features) VALUES
(1, 'Carte Steam 50€', 'carte-steam-50', 'Ajoutez 50€ à votre portefeuille Steam pour acheter vos jeux préférés', 'Carte Steam 50€ - Livraison instantanée', 50.00, 52.00, 'https://via.placeholder.com/300x200/1e3a8a/ffffff?text=Steam+50€', 'PROMO', 'bg-red-500', 'Instantané', 'Steam', 4.9, 'in_stock', TRUE, '["Livraison instantanée", "Code officiel Steam", "Valable dans le monde entier"]'),
(1, 'PlayStation Plus 12 mois', 'playstation-plus-12-mois', 'Abonnement PlayStation Plus pour 12 mois complets', 'PlayStation Plus 12 mois - Jeux gratuits', 59.99, 69.99, 'https://via.placeholder.com/300x200/003087/ffffff?text=PS+Plus+12m', 'POPULAIRE', 'bg-blue-500', '5-10 min', 'PlayStation', 4.8, 'in_stock', TRUE, '["Jeux gratuits mensuels", "Multijoueur en ligne", "Remises exclusives"]'),
(2, 'Netflix Premium 6 mois', 'netflix-premium-6-mois', 'Abonnement Netflix Premium pour 6 mois', 'Netflix Premium 6 mois - 4K Ultra HD', 89.99, 95.94, 'https://via.placeholder.com/300x200/e50914/ffffff?text=Netflix+6m', 'ÉCONOMIE', 'bg-green-500', '2-5 min', 'Netflix', 4.8, 'in_stock', TRUE, '["4K Ultra HD", "4 écrans simultanés", "Téléchargement hors ligne"]'),
(3, 'Microsoft Office 2021 Pro', 'microsoft-office-2021-pro', 'Suite bureautique complète Microsoft Office 2021 Professionnel', 'Office 2021 Pro - Word, Excel, PowerPoint', 49.99, 439.99, 'https://via.placeholder.com/300x200/0078d4/ffffff?text=Office+2021', 'MEGA PROMO', 'bg-red-600', 'Instantané', 'Microsoft', 4.9, 'in_stock', TRUE, '["Word", "Excel", "PowerPoint", "Outlook", "Access", "Publisher"]');

-- Insertion des paramètres par défaut
INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
('site_name', 'CREE 2GK', 'text'),
('site_description', 'Plateforme de produits numériques avec livraison instantanée', 'text'),
('contact_email', 'contact@cree2gk.com', 'text'),
('support_email', 'support@cree2gk.com', 'text'),
('maintenance_mode', '0', 'boolean'),
('registration_enabled', '1', 'boolean'),
('email_verification_required', '1', 'boolean');

-- Index pour optimiser les performances
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_digital_codes_product ON digital_codes(product_id);
CREATE INDEX idx_digital_codes_status ON digital_codes(status);
CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_cart_user ON cart(user_id);
CREATE INDEX idx_cart_session ON cart(session_id);