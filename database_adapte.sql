-- ================================================================
-- MAICKELSMM - Script SQL Adapté (Sans colonne username)
-- Compatible avec votre structure DB actuelle
-- Date: 6 Août 2025
-- ================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- ================================================================
-- TABLE: users (Structure adaptée)
-- ================================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin','superadmin') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `email_verification_token` varchar(255) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: categories
-- ================================================================
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: services
-- ================================================================
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price_per_1000` decimal(10,2) NOT NULL,
  `min_order` int(11) NOT NULL DEFAULT 100,
  `max_order` int(11) NOT NULL DEFAULT 10000,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: orders
-- ================================================================
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `link` varchar(500) NOT NULL,
  `start_count` int(11) DEFAULT 0,
  `remains` int(11) DEFAULT 0,
  `charge` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','in_progress','completed','partial','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(100) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `notes` text,
  `guest_email` varchar(255) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `service_id` (`service_id`),
  KEY `status` (`status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: settings
-- ================================================================
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: messages (Support client)
-- ================================================================
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','closed') NOT NULL DEFAULT 'new',
  `admin_reply` text,
  `replied_at` datetime DEFAULT NULL,
  `replied_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- TABLE: logs (Journal système)
-- ================================================================
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- DONNÉES INITIALES
-- ================================================================

-- Utilisateur Admin (SANS username)
INSERT INTO `users` (`email`, `password`, `first_name`, `last_name`, `role`, `status`, `email_verified`) VALUES
('admin@maickelsmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'MaickelSMM', 'superadmin', 'active', 1);
-- Mot de passe: password123

-- Paramètres système
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_name', 'MaickelSMM', 'Nom du site'),
('site_description', 'Services SMM professionnels - Followers, Likes, Vues et plus', 'Description du site'),
('site_keywords', 'smm, social media, followers, likes, instagram, facebook, youtube', 'Mots-clés SEO'),
('currency', 'FCFA', 'Devise utilisée'),
('currency_symbol', 'FCFA', 'Symbole de la devise'),
('registration_enabled', '1', 'Autoriser les inscriptions (1=oui, 0=non)'),
('maintenance_mode', '0', 'Mode maintenance (1=oui, 0=non)'),
('contact_email', 'contact@maickelsmm.com', 'Email de contact'),
('support_email', 'support@maickelsmm.com', 'Email de support'),
('phone_number', '+237 6XX XXX XXX', 'Numéro de téléphone'),
('whatsapp_number', '+237 6XX XXX XXX', 'Numéro WhatsApp'),
('facebook_page', 'https://facebook.com/maickelsmm', 'Page Facebook'),
('instagram_page', 'https://instagram.com/maickelsmm', 'Page Instagram'),
('telegram_channel', 'https://t.me/maickelsmm', 'Canal Telegram'),
('payment_instructions', 'Effectuez le paiement via Mobile Money puis téléchargez la preuve de paiement.', 'Instructions de paiement'),
('momo_mtn_number', '+237 6XX XXX XXX', 'Numéro MTN Mobile Money'),
('momo_orange_number', '+237 6XX XXX XXX', 'Numéro Orange Money'),
('min_order_amount', '500', 'Montant minimum de commande'),
('max_order_amount', '50000', 'Montant maximum de commande');

-- Catégories de services
INSERT INTO `categories` (`name`, `description`, `icon`, `sort_order`) VALUES
('Instagram', 'Services pour Instagram - Followers, Likes, Vues', 'fab fa-instagram', 1),
('Facebook', 'Services pour Facebook - Likes, Followers, Partages', 'fab fa-facebook', 2),
('YouTube', 'Services pour YouTube - Vues, Abonnés, Likes', 'fab fa-youtube', 3),
('TikTok', 'Services pour TikTok - Followers, Likes, Vues', 'fab fa-tiktok', 4),
('Twitter', 'Services pour Twitter - Followers, Likes, Retweets', 'fab fa-twitter', 5),
('LinkedIn', 'Services pour LinkedIn - Connections, Likes', 'fab fa-linkedin', 6),
('Telegram', 'Services pour Telegram - Membres, Vues', 'fab fa-telegram', 7),
('WhatsApp', 'Services pour WhatsApp - Groupes, Statuts', 'fab fa-whatsapp', 8);

-- Services Instagram
INSERT INTO `services` (`category_id`, `name`, `description`, `price_per_1000`, `min_order`, `max_order`) VALUES
(1, 'Instagram Followers [Qualité Premium]', 'Followers Instagram de haute qualité, livraison progressive', 2500.00, 100, 10000),
(1, 'Instagram Likes [Ultra Rapide]', 'Likes Instagram livrés en moins de 1 heure', 1500.00, 50, 5000),
(1, 'Instagram Vues Stories', 'Vues sur vos stories Instagram', 800.00, 100, 10000),
(1, 'Instagram Comments [Personnalisés]', 'Commentaires personnalisés en français', 5000.00, 10, 500),
(1, 'Instagram Views Reels', 'Vues sur vos Reels Instagram', 1200.00, 100, 50000);

-- Services Facebook
INSERT INTO `services` (`category_id`, `name`, `description`, `price_per_1000`, `min_order`, `max_order`) VALUES
(2, 'Facebook Page Likes', 'Likes sur votre page Facebook', 2000.00, 100, 5000),
(2, 'Facebook Post Likes', 'Likes sur vos publications Facebook', 1800.00, 50, 2000),
(2, 'Facebook Followers', 'Followers pour votre profil Facebook', 2200.00, 100, 5000),
(2, 'Facebook Shares', 'Partages de vos publications', 3000.00, 20, 1000),
(2, 'Facebook Video Views', 'Vues sur vos vidéos Facebook', 1000.00, 1000, 100000);

-- Services YouTube
INSERT INTO `services` (`category_id`, `name`, `description`, `price_per_1000`, `min_order`, `max_order`) VALUES
(3, 'YouTube Views [High Retention]', 'Vues YouTube avec rétention élevée', 1500.00, 1000, 100000),
(3, 'YouTube Subscribers', 'Abonnés YouTube réels et actifs', 8000.00, 50, 2000),
(3, 'YouTube Likes', 'Likes sur vos vidéos YouTube', 2500.00, 50, 5000),
(3, 'YouTube Comments', 'Commentaires positifs sur vos vidéos', 10000.00, 5, 100),
(3, 'YouTube Watch Time', 'Temps de visionnage pour monétisation', 5000.00, 100, 10000);

-- Services TikTok
INSERT INTO `services` (`category_id`, `name`, `description`, `price_per_1000`, `min_order`, `max_order`) VALUES
(4, 'TikTok Followers [Premium]', 'Followers TikTok de qualité supérieure', 3000.00, 100, 10000),
(4, 'TikTok Likes [Rapide]', 'Likes TikTok livrés rapidement', 1200.00, 100, 10000),
(4, 'TikTok Views', 'Vues sur vos vidéos TikTok', 800.00, 1000, 100000),
(4, 'TikTok Shares', 'Partages de vos vidéos TikTok', 2500.00, 50, 5000),
(4, 'TikTok Comments [FR]', 'Commentaires en français', 8000.00, 10, 500);

-- Services Twitter
INSERT INTO `services` (`category_id`, `name`, `description`, `price_per_1000`, `min_order`, `max_order`) VALUES
(5, 'Twitter Followers', 'Followers Twitter actifs', 4000.00, 100, 5000),
(5, 'Twitter Likes', 'Likes sur vos tweets', 2000.00, 50, 5000),
(5, 'Twitter Retweets', 'Retweets de vos publications', 3500.00, 25, 2000),
(5, 'Twitter Impressions', 'Impressions sur vos tweets', 1000.00, 1000, 50000);

-- Services LinkedIn
INSERT INTO `services` (`category_id`, `name`, `description`, `price_per_1000`, `min_order`, `max_order`) VALUES
(6, 'LinkedIn Connections', 'Connexions LinkedIn professionnelles', 6000.00, 50, 1000),
(6, 'LinkedIn Post Likes', 'Likes sur vos publications LinkedIn', 4000.00, 25, 1000),
(6, 'LinkedIn Followers', 'Followers pour votre profil LinkedIn', 5000.00, 100, 2000);

-- Services Telegram
INSERT INTO `services` (`category_id`, `name`, `description`, `price_per_1000`, `min_order`, `max_order`) VALUES
(7, 'Telegram Members', 'Membres pour votre canal Telegram', 3500.00, 100, 10000),
(7, 'Telegram Post Views', 'Vues sur vos posts Telegram', 1200.00, 100, 50000);

-- Services WhatsApp
INSERT INTO `services` (`category_id`, `name`, `description`, `price_per_1000`, `min_order`, `max_order`) VALUES
(8, 'WhatsApp Group Members', 'Membres pour vos groupes WhatsApp', 4000.00, 50, 500),
(8, 'WhatsApp Status Views', 'Vues sur vos statuts WhatsApp', 1500.00, 100, 5000);

-- ================================================================
-- VUES UTILES
-- ================================================================

-- Vue pour les statistiques admin
CREATE VIEW `admin_stats` AS
SELECT 
    (SELECT COUNT(*) FROM users WHERE role = 'user') AS total_users,
    (SELECT COUNT(*) FROM orders) AS total_orders,
    (SELECT COUNT(*) FROM orders WHERE status = 'pending') AS pending_orders,
    (SELECT COUNT(*) FROM orders WHERE status = 'completed') AS completed_orders,
    (SELECT COUNT(*) FROM services WHERE status = 'active') AS active_services,
    (SELECT COUNT(*) FROM categories WHERE status = 'active') AS active_categories,
    (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled') AS total_revenue;

-- ================================================================
-- INDEX POUR PERFORMANCES
-- ================================================================

-- Index pour recherches fréquentes
CREATE INDEX idx_orders_status_created ON orders(status, created_at);
CREATE INDEX idx_users_role_status ON users(role, status);
CREATE INDEX idx_services_category_status ON services(category_id, status);
CREATE INDEX idx_orders_user_status ON orders(user_id, status);

-- ================================================================
-- TRIGGERS POUR LOGS
-- ================================================================

DELIMITER //

-- Trigger pour logger les connexions
CREATE TRIGGER user_login_log 
AFTER UPDATE ON users 
FOR EACH ROW 
BEGIN
    IF NEW.last_login != OLD.last_login THEN
        INSERT INTO logs (user_id, action, description) 
        VALUES (NEW.id, 'user_login', CONCAT('Connexion utilisateur: ', NEW.email));
    END IF;
END//

-- Trigger pour logger les nouvelles commandes
CREATE TRIGGER order_created_log 
AFTER INSERT ON orders 
FOR EACH ROW 
BEGIN
    INSERT INTO logs (user_id, action, description) 
    VALUES (NEW.user_id, 'order_created', CONCAT('Nouvelle commande #', NEW.id, ' - Montant: ', NEW.total_amount, ' FCFA'));
END//

DELIMITER ;

-- ================================================================
-- FINALISATION
-- ================================================================

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ================================================================
-- INFORMATIONS IMPORTANTES
-- ================================================================

/*
UTILISATEUR ADMIN PAR DÉFAUT:
- Email: admin@maickelsmm.com
- Mot de passe: password123

NOTES IMPORTANTES:
1. Cette structure N'UTILISE PAS de colonne 'username'
2. L'authentification se fait UNIQUEMENT par email
3. Tous les fichiers minimaux sont compatibles
4. La table 'settings' permet la configuration via l'admin
5. Le système de logs track toutes les actions importantes

APRÈS IMPORT:
1. Testez: detect_db_config.php
2. Testez: check_users_table.php  
3. Connectez-vous: login_minimal.php
4. Accédez à l'admin: admin_minimal.php

SÉCURITÉ:
- Changez le mot de passe admin après la première connexion
- Modifiez les numéros de téléphone dans les settings
- Configurez vos vraies informations de paiement
*/