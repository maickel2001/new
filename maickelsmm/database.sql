-- Base de données MaickelSMM - Clone amélioré de TarantulaSMM
-- Création de la base de données et des tables

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Structure de la table `categories`
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `services`
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `min_quantity` int(11) NOT NULL DEFAULT 100,
  `max_quantity` int(11) NOT NULL DEFAULT 10000,
  `price_per_1000` decimal(10,2) NOT NULL,
  `delivery_time` varchar(50) NOT NULL DEFAULT '1-3 jours',
  `guarantee` enum('yes','no') DEFAULT 'yes',
  `status` enum('active','inactive') DEFAULT 'active',
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `role` enum('user','admin','superadmin') DEFAULT 'user',
  `status` enum('active','inactive','blocked') DEFAULT 'active',
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `orders`
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `link` varchar(500) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled','refunded') DEFAULT 'pending',
  `start_count` int(11) DEFAULT 0,
  `remains` int(11) DEFAULT 0,
  `notes` text,
  `guest_name` varchar(100) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `service_id` (`service_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `payments`
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'XOF',
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `payment_proof` varchar(255) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `settings`
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text,
  `setting_type` enum('text','textarea','number','boolean','json') DEFAULT 'text',
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `pages`
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `content` longtext,
  `meta_description` text,
  `meta_keywords` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `contact_messages`
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `admin_logs`
CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des catégories
INSERT INTO `categories` (`name`, `description`, `icon`, `sort_order`, `status`) VALUES
('Instagram', 'Services pour Instagram - Followers, Likes, Vues, Commentaires', 'fab fa-instagram', 1, 'active'),
('TikTok', 'Services pour TikTok - Followers, Likes, Vues, Commentaires', 'fab fa-tiktok', 2, 'active'),
('YouTube', 'Services pour YouTube - Abonnés, Vues, Likes, Commentaires', 'fab fa-youtube', 3, 'active'),
('Facebook', 'Services pour Facebook - Amis, Abonnés, Likes, Partages', 'fab fa-facebook', 4, 'active'),
('Twitter', 'Services pour Twitter - Followers, Likes, Retweets', 'fab fa-twitter', 5, 'active'),
('LinkedIn', 'Services pour LinkedIn - Connexions, Visites profil', 'fab fa-linkedin', 6, 'active'),
('Snapchat', 'Services pour Snapchat - Followers, Vues stories', 'fab fa-snapchat', 7, 'active'),
('Spotify', 'Services pour Spotify - Followers, Streams, Likes', 'fab fa-spotify', 8, 'active'),
('Twitch', 'Services pour Twitch - Followers, Viewers, Bits', 'fab fa-twitch', 9, 'active'),
('Pinterest', 'Services pour Pinterest - Followers, Likes, Repins', 'fab fa-pinterest', 10, 'active'),
('Reddit', 'Services pour Reddit - Upvotes, Commentaires, Abonnés', 'fab fa-reddit', 11, 'active'),
('Telegram', 'Services pour Telegram - Membres, Réactions', 'fab fa-telegram', 12, 'active'),
('Clubhouse', 'Services pour Clubhouse - Followers, Invitations', 'fas fa-microphone', 13, 'active'),
('WhatsApp', 'Services pour WhatsApp - Membres groupes, Vues statuts', 'fab fa-whatsapp', 14, 'active'),
('Autres Services', 'Services marketing divers - Email, SMS, Trafic web', 'fas fa-globe', 15, 'active');

-- Insertion des services Instagram
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(1, 'Followers Instagram Réels', 'Followers Instagram 100% réels avec garantie 30 jours. Profils actifs et authentiques.', 100, 50000, 2500.00, '1-3 jours', 'yes', 1),
(1, 'Followers Instagram Ciblés France', 'Followers Instagram ciblés géographiquement en France. Profils réels et actifs.', 50, 10000, 3500.00, '2-5 jours', 'yes', 2),
(1, 'Followers Instagram Ciblés Afrique', 'Followers Instagram ciblés en Afrique francophone. Profils authentiques.', 50, 15000, 2000.00, '1-4 jours', 'yes', 3),
(1, 'Likes Instagram Photo', 'Likes Instagram pour vos photos. Livraison rapide et sécurisée.', 50, 20000, 500.00, '0-1 heure', 'yes', 4),
(1, 'Likes Instagram Vidéo', 'Likes Instagram pour vos vidéos et reels. Boost instantané.', 50, 20000, 600.00, '0-1 heure', 'yes', 5),
(1, 'Vues Instagram Stories', 'Vues pour vos stories Instagram. Augmentez votre visibilité.', 100, 50000, 300.00, '0-30 minutes', 'no', 6),
(1, 'Vues Instagram Reels', 'Vues pour vos reels Instagram. Devenez viral rapidement.', 100, 100000, 250.00, '0-1 heure', 'no', 7),
(1, 'Commentaires Instagram Personnalisés', 'Commentaires Instagram personnalisés en français. Messages positifs et engageants.', 5, 500, 15000.00, '1-3 jours', 'yes', 8),
(1, 'Partages Instagram', 'Partages Instagram pour vos publications. Augmentez votre portée.', 10, 5000, 2000.00, '1-6 heures', 'no', 9),
(1, 'Sauvegardes Instagram', 'Sauvegardes Instagram pour vos publications. Signal fort d\'engagement.', 25, 10000, 1500.00, '1-12 heures', 'no', 10),
(1, 'Visionnages IGTV', 'Vues pour vos vidéos IGTV. Boostez vos contenus longs.', 100, 50000, 400.00, '1-6 heures', 'no', 11);

-- Insertion des services TikTok
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(2, 'Followers TikTok Réels', 'Followers TikTok 100% réels avec garantie. Profils actifs et authentiques.', 100, 100000, 1800.00, '1-3 jours', 'yes', 1),
(2, 'Followers TikTok Ciblés', 'Followers TikTok ciblés par pays et démographie. Audience qualifiée.', 50, 20000, 2800.00, '2-5 jours', 'yes', 2),
(2, 'Likes TikTok', 'Likes TikTok pour vos vidéos. Boost instantané de popularité.', 50, 50000, 400.00, '0-1 heure', 'yes', 3),
(2, 'Vues TikTok Vidéos', 'Vues TikTok pour vos vidéos. Devenez viral sur TikTok.', 1000, 1000000, 150.00, '0-30 minutes', 'no', 4),
(2, 'Vues TikTok Reels', 'Vues spéciales pour vos reels TikTok. Maximisez votre visibilité.', 1000, 500000, 200.00, '0-1 heure', 'no', 5),
(2, 'Commentaires TikTok', 'Commentaires TikTok personnalisés. Messages engageants et positifs.', 5, 1000, 12000.00, '1-3 jours', 'yes', 6),
(2, 'Partages TikTok', 'Partages TikTok pour vos vidéos. Augmentez votre portée organique.', 10, 10000, 1800.00, '1-6 heures', 'no', 7);

-- Insertion des services YouTube
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(3, 'Abonnés YouTube Réels', 'Abonnés YouTube 100% réels avec garantie 30 jours. Croissance naturelle.', 50, 20000, 8000.00, '3-7 jours', 'yes', 1),
(3, 'Abonnés YouTube Ciblés', 'Abonnés YouTube ciblés par niche et géographie. Audience qualifiée.', 25, 5000, 12000.00, '5-10 jours', 'yes', 2),
(3, 'Vues YouTube Vidéos', 'Vues YouTube pour vos vidéos. Boost de visibilité et ranking.', 1000, 1000000, 800.00, '1-24 heures', 'no', 3),
(3, 'Vues YouTube Shorts', 'Vues spéciales pour YouTube Shorts. Maximisez votre reach.', 1000, 500000, 600.00, '1-12 heures', 'no', 4),
(3, 'Likes YouTube', 'Likes YouTube pour vos vidéos. Signal positif pour l\'algorithme.', 50, 20000, 2000.00, '1-6 heures', 'yes', 5),
(3, 'Commentaires YouTube', 'Commentaires YouTube personnalisés. Messages pertinents et engageants.', 5, 500, 20000.00, '1-5 jours', 'yes', 6),
(3, 'Partages YouTube', 'Partages YouTube pour vos vidéos. Augmentez votre portée.', 10, 5000, 3000.00, '1-12 heures', 'no', 7),
(3, 'Temps de Visionnage', 'Temps de visionnage YouTube. Améliorez vos métriques de rétention.', 100, 10000, 5000.00, '1-3 jours', 'no', 8);

-- Insertion des services Facebook
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(4, 'Amis Facebook', 'Demandes d\'amis Facebook acceptées. Profils réels et actifs.', 50, 5000, 4000.00, '1-5 jours', 'yes', 1),
(4, 'Abonnés Facebook Page', 'Abonnés Facebook pour votre page professionnelle. Croissance organique.', 100, 50000, 2200.00, '1-3 jours', 'yes', 2),
(4, 'Likes Facebook Posts', 'Likes Facebook pour vos publications. Boost d\'engagement instantané.', 50, 20000, 600.00, '0-2 heures', 'yes', 3),
(4, 'Likes Facebook Page', 'Likes Facebook pour votre page. Augmentez votre crédibilité.', 100, 20000, 1800.00, '1-3 jours', 'yes', 4),
(4, 'Partages Facebook', 'Partages Facebook pour vos publications. Maximisez votre portée.', 10, 5000, 2500.00, '1-6 heures', 'no', 5),
(4, 'Commentaires Facebook', 'Commentaires Facebook personnalisés. Messages engageants et positifs.', 5, 500, 18000.00, '1-3 jours', 'yes', 6),
(4, 'Invitations Page Facebook', 'Invitations à aimer votre page Facebook. Croissance ciblée.', 100, 10000, 1200.00, '1-2 jours', 'no', 7);

-- Insertion des services Twitter
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(5, 'Followers Twitter Réels', 'Followers Twitter 100% réels avec garantie. Profils actifs et authentiques.', 100, 50000, 3500.00, '1-3 jours', 'yes', 1),
(5, 'Likes Twitter Tweets', 'Likes Twitter pour vos tweets. Boost d\'engagement instantané.', 50, 20000, 800.00, '0-1 heure', 'yes', 2),
(5, 'Retweets Twitter', 'Retweets Twitter pour vos tweets. Augmentez votre portée.', 25, 10000, 1500.00, '1-6 heures', 'no', 3),
(5, 'Commentaires Twitter', 'Commentaires Twitter personnalisés. Réponses engageantes.', 5, 500, 25000.00, '1-3 jours', 'yes', 4),
(5, 'Mentions Twitter', 'Mentions Twitter pour augmenter votre visibilité.', 10, 1000, 8000.00, '1-2 jours', 'no', 5);

-- Insertion des services LinkedIn
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(6, 'Connexions LinkedIn', 'Connexions LinkedIn professionnelles. Développez votre réseau.', 50, 2000, 15000.00, '3-7 jours', 'yes', 1),
(6, 'Visites Profil LinkedIn', 'Visites de profil LinkedIn. Augmentez votre visibilité professionnelle.', 100, 5000, 5000.00, '1-3 jours', 'no', 2),
(6, 'Recommandations LinkedIn', 'Recommandations LinkedIn professionnelles. Renforcez votre crédibilité.', 5, 100, 50000.00, '3-7 jours', 'yes', 3),
(6, 'Endorsements LinkedIn', 'Endorsements pour vos compétences LinkedIn. Validez votre expertise.', 10, 500, 8000.00, '1-3 jours', 'yes', 4);

-- Insertion des services Snapchat
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(7, 'Followers Snapchat', 'Followers Snapchat réels. Augmentez votre audience.', 100, 10000, 4500.00, '2-5 jours', 'yes', 1),
(7, 'Vues Stories Snapchat', 'Vues pour vos stories Snapchat. Boostez votre visibilité.', 100, 50000, 1200.00, '1-6 heures', 'no', 2);

-- Insertion des services Spotify
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(8, 'Followers Spotify', 'Followers Spotify pour votre profil artiste. Développez votre fanbase.', 50, 10000, 6000.00, '2-5 jours', 'yes', 1),
(8, 'Lectures Spotify Streams', 'Streams Spotify pour vos titres. Boostez vos statistiques.', 1000, 1000000, 1500.00, '1-3 jours', 'no', 2),
(8, 'Likes Playlists Spotify', 'Likes pour vos playlists Spotify. Augmentez leur popularité.', 50, 5000, 3000.00, '1-3 jours', 'yes', 3);

-- Insertion des services Twitch
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(9, 'Followers Twitch', 'Followers Twitch pour votre chaîne. Développez votre communauté.', 50, 20000, 5500.00, '1-3 jours', 'yes', 1),
(9, 'Viewers Twitch Live', 'Viewers Twitch pour vos streams live. Boostez votre audience.', 10, 1000, 25000.00, '0-1 heure', 'no', 2),
(9, 'Bits Twitch Simulés', 'Bits Twitch simulés pour vos streams. Augmentez l\'engagement.', 100, 10000, 8000.00, '1-6 heures', 'no', 3);

-- Insertion des services Pinterest
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(10, 'Followers Pinterest', 'Followers Pinterest pour votre profil. Développez votre audience.', 100, 20000, 3800.00, '1-3 jours', 'yes', 1),
(10, 'Likes Pins Pinterest', 'Likes Pinterest pour vos pins. Boostez leur popularité.', 50, 10000, 1500.00, '1-6 heures', 'yes', 2),
(10, 'Repins Pinterest', 'Repins Pinterest pour vos épingles. Maximisez leur diffusion.', 25, 5000, 2200.00, '1-12 heures', 'no', 3);

-- Insertion des services Reddit
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(11, 'Upvotes Reddit', 'Upvotes Reddit pour vos posts. Augmentez leur visibilité.', 10, 5000, 3500.00, '1-6 heures', 'no', 1),
(11, 'Commentaires Reddit', 'Commentaires Reddit engageants pour vos posts.', 5, 200, 30000.00, '1-3 jours', 'yes', 2),
(11, 'Abonnés Subreddit', 'Abonnés pour votre subreddit. Développez votre communauté.', 50, 10000, 4200.00, '2-5 jours', 'yes', 3);

-- Insertion des services Telegram
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(12, 'Membres Groupe Telegram', 'Membres Telegram pour votre groupe. Développez votre communauté.', 100, 50000, 1800.00, '1-3 jours', 'yes', 1),
(12, 'Membres Channel Telegram', 'Abonnés Telegram pour votre channel. Augmentez votre audience.', 100, 100000, 1500.00, '1-3 jours', 'yes', 2),
(12, 'Réactions Messages Telegram', 'Réactions Telegram pour vos messages. Boostez l\'engagement.', 50, 10000, 800.00, '1-6 heures', 'no', 3);

-- Insertion des services Clubhouse
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(13, 'Followers Clubhouse', 'Followers Clubhouse pour votre profil. Développez votre réseau.', 50, 5000, 8000.00, '3-7 jours', 'yes', 1),
(13, 'Invitations Clubhouse', 'Invitations Clubhouse pour vos événements. Augmentez la participation.', 10, 500, 15000.00, '1-3 jours', 'no', 2);

-- Insertion des services WhatsApp
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(14, 'Membres Groupes WhatsApp', 'Membres WhatsApp pour vos groupes. Développez votre communauté.', 50, 5000, 6000.00, '2-5 jours', 'yes', 1),
(14, 'Vues Statuts WhatsApp', 'Vues WhatsApp pour vos statuts. Augmentez votre visibilité.', 100, 10000, 2000.00, '1-6 heures', 'no', 2);

-- Insertion des autres services
INSERT INTO `services` (`category_id`, `name`, `description`, `min_quantity`, `max_quantity`, `price_per_1000`, `delivery_time`, `guarantee`, `sort_order`) VALUES
(15, 'Emails Marketing Ciblés', 'Campagnes emails marketing ciblées. Atteignez votre audience.', 1000, 100000, 500.00, '1-2 jours', 'no', 1),
(15, 'SMS Marketing Ciblés', 'Campagnes SMS marketing ciblées. Communication directe.', 100, 50000, 2500.00, '1-2 jours', 'no', 2),
(15, 'Trafic Site Web', 'Trafic web ciblé pour votre site. Augmentez vos visites.', 1000, 500000, 800.00, '1-3 jours', 'no', 3),
(15, 'Téléchargements Apps Mobiles', 'Téléchargements pour vos applications mobiles. Boostez vos stats.', 100, 20000, 5000.00, '2-5 jours', 'no', 4),
(15, 'Avis Google My Business', 'Avis Google positifs pour votre entreprise. Améliorez votre réputation.', 5, 100, 80000.00, '3-7 jours', 'yes', 5),
(15, 'Avis Trustpilot', 'Avis Trustpilot positifs. Renforcez votre crédibilité en ligne.', 5, 50, 100000.00, '3-7 jours', 'yes', 6),
(15, 'Avis Facebook Entreprise', 'Avis Facebook positifs pour votre page entreprise.', 5, 100, 60000.00, '2-5 jours', 'yes', 7),
(15, 'Téléchargements Fichiers', 'Téléchargements pour vos fichiers et logiciels. Boostez vos stats.', 100, 50000, 1200.00, '1-3 jours', 'no', 8);

-- Insertion des paramètres par défaut
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_name', 'MaickelSMM', 'text', 'Nom du site'),
('site_description', 'Panneau SMM professionnel - Services de marketing des réseaux sociaux', 'textarea', 'Description du site'),
('site_logo', '', 'text', 'Logo du site'),
('site_favicon', '', 'text', 'Favicon du site'),
('currency', 'XOF', 'text', 'Devise utilisée'),
('currency_symbol', 'FCFA', 'text', 'Symbole de la devise'),
('timezone', 'Africa/Abidjan', 'text', 'Fuseau horaire'),
('maintenance_mode', '0', 'boolean', 'Mode maintenance'),
('allow_registration', '1', 'boolean', 'Autoriser les inscriptions'),
('min_deposit', '5000', 'number', 'Dépôt minimum en FCFA'),
('payment_methods', '{"mtn":"67890123","moov":"60123456","orange":"07654321"}', 'json', 'Méthodes de paiement mobile'),
('payment_instructions', 'Envoyez le montant exact via Mobile Money puis uploadez la preuve de paiement.', 'textarea', 'Instructions de paiement'),
('contact_email', 'contact@maickelsmm.com', 'text', 'Email de contact'),
('contact_phone', '+225 07 12 34 56 78', 'text', 'Téléphone de contact'),
('contact_whatsapp', '+22507123456', 'text', 'WhatsApp de contact'),
('home_title', 'MaickelSMM - Panneau SMM Professionnel', 'text', 'Titre de la page d\'accueil'),
('home_subtitle', 'Boostez votre présence sur les réseaux sociaux avec nos services de qualité', 'text', 'Sous-titre de la page d\'accueil'),
('home_features', '["Services de qualité premium","Livraison rapide et fiable","Support client 24/7","Prix compétitifs","Garantie de remboursement"]', 'json', 'Fonctionnalités mises en avant'),
('smtp_host', '', 'text', 'Serveur SMTP'),
('smtp_port', '587', 'text', 'Port SMTP'),
('smtp_username', '', 'text', 'Nom d\'utilisateur SMTP'),
('smtp_password', '', 'text', 'Mot de passe SMTP'),
('email_notifications', '1', 'boolean', 'Notifications email activées');

-- Insertion des pages par défaut
INSERT INTO `pages` (`title`, `slug`, `content`, `status`) VALUES
('À propos', 'about', '<h2>À propos de MaickelSMM</h2><p>MaickelSMM est votre partenaire de confiance pour tous vos besoins en marketing des réseaux sociaux. Nous offrons des services de qualité premium pour booster votre présence en ligne.</p>', 'active'),
('Politique de confidentialité', 'privacy', '<h2>Politique de confidentialité</h2><p>Votre vie privée est importante pour nous. Cette politique explique comment nous collectons et utilisons vos informations.</p>', 'active'),
('Conditions d\'utilisation', 'terms', '<h2>Conditions d\'utilisation</h2><p>En utilisant nos services, vous acceptez ces conditions d\'utilisation.</p>', 'active'),
('FAQ', 'faq', '<h2>Questions fréquemment posées</h2><h3>Comment passer une commande ?</h3><p>Sélectionnez un service, entrez la quantité et le lien, puis procédez au paiement.</p>', 'active');

-- Création de l'utilisateur admin par défaut
INSERT INTO `users` (`username`, `email`, `password`, `first_name`, `last_name`, `role`, `status`, `email_verified`) VALUES
('admin', 'admin@maickelsmm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'MaickelSMM', 'superadmin', 'active', 1);

COMMIT;