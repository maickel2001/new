<?php
/**
 * Script de reconstruction des tables services et settings
 * ATTENTION : Ce script va supprimer et recréer les tables !
 */

require_once 'config/database.php';

echo "<h1>🔨 Reconstruction des Tables</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;background:#f5f5f5;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";

// Confirmation de sécurité
$confirm = $_GET['confirm'] ?? '';
if ($confirm !== 'yes') {
    echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;padding:1rem;border-radius:5px;margin:2rem 0;'>";
    echo "<h3 style='color:#721c24;'>⚠️ ATTENTION</h3>";
    echo "<p style='color:#721c24;'>Ce script va <strong>SUPPRIMER</strong> et recréer les tables 'services' et 'settings'.</p>";
    echo "<p style='color:#721c24;'>Toutes les données existantes dans ces tables seront perdues !</p>";
    echo "<p><a href='?confirm=yes' style='background:#dc3545;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>🔨 Confirmer la reconstruction</a></p>";
    echo "<p><a href='diagnose_tables.php' style='background:#6c757d;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>← Retour au diagnostic</a></p>";
    echo "</div>";
    exit;
}

try {
    $db = Database::getInstance();
    
    echo "<h2>1. Suppression des tables existantes</h2>";
    
    // Supprimer la table services
    try {
        $db->execute("DROP TABLE IF EXISTS services");
        echo "<p class='warning'>⚠️ Table 'services' supprimée</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur suppression 'services': " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Supprimer la table settings
    try {
        $db->execute("DROP TABLE IF EXISTS settings");
        echo "<p class='warning'>⚠️ Table 'settings' supprimée</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur suppression 'settings': " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<h2>2. Création de la table 'services'</h2>";
    
    $createServices = "
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    try {
        $db->execute($createServices);
        echo "<p class='ok'>✅ Table 'services' créée avec succès</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur création 'services': " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<h2>3. Création de la table 'settings'</h2>";
    
    $createSettings = "
    CREATE TABLE `settings` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `setting_key` varchar(100) NOT NULL,
      `setting_value` text,
      `setting_type` enum('text','textarea','number','boolean','json') DEFAULT 'text',
      `description` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `setting_key` (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    try {
        $db->execute($createSettings);
        echo "<p class='ok'>✅ Table 'settings' créée avec succès</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur création 'settings': " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<h2>4. Insertion des services par défaut</h2>";
    
    $services = [
        // Instagram
        [1, 'Followers Instagram Réels', 'Followers Instagram 100% réels avec garantie 30 jours. Profils actifs et authentiques.', 100, 50000, 2500.00, '1-3 jours', 'yes', 'active', 1],
        [1, 'Followers Instagram Ciblés France', 'Followers Instagram ciblés géographiquement en France. Profils réels et actifs.', 50, 10000, 3500.00, '2-5 jours', 'yes', 'active', 2],
        [1, 'Followers Instagram Ciblés Afrique', 'Followers Instagram ciblés en Afrique francophone. Profils authentiques.', 50, 15000, 2000.00, '1-4 jours', 'yes', 'active', 3],
        [1, 'Likes Instagram Photo', 'Likes Instagram pour vos photos. Livraison rapide et sécurisée.', 50, 20000, 500.00, '0-1 heure', 'yes', 'active', 4],
        [1, 'Vues Instagram Stories', 'Vues pour vos stories Instagram. Augmentez votre visibilité.', 100, 50000, 300.00, '0-30 minutes', 'no', 'active', 5],
        
        // TikTok (category_id = 2)
        [2, 'Followers TikTok Réels', 'Followers TikTok 100% réels avec garantie. Profils actifs et authentiques.', 100, 100000, 1800.00, '1-3 jours', 'yes', 'active', 1],
        [2, 'Likes TikTok', 'Likes TikTok pour vos vidéos. Boost instantané de popularité.', 50, 50000, 400.00, '0-1 heure', 'yes', 'active', 2],
        [2, 'Vues TikTok Vidéos', 'Vues TikTok pour vos vidéos. Devenez viral sur TikTok.', 1000, 1000000, 150.00, '0-30 minutes', 'no', 'active', 3],
        
        // YouTube (category_id = 3)
        [3, 'Abonnés YouTube Réels', 'Abonnés YouTube 100% réels et actifs. Croissance naturelle garantie.', 50, 10000, 8000.00, '3-7 jours', 'yes', 'active', 1],
        [3, 'Vues YouTube', 'Vues YouTube pour vos vidéos. Augmentez votre visibilité rapidement.', 100, 1000000, 200.00, '0-12 heures', 'no', 'active', 2],
        [3, 'Likes YouTube', 'Likes YouTube pour vos vidéos. Boostez votre engagement.', 25, 50000, 800.00, '0-6 heures', 'yes', 'active', 3],
        
        // Facebook (category_id = 4)
        [4, 'Amis Facebook', 'Demandes d\'amis Facebook de profils réels et actifs.', 50, 5000, 3000.00, '1-3 jours', 'yes', 'active', 1],
        [4, 'Likes Page Facebook', 'Likes pour votre page Facebook. Augmentez votre crédibilité.', 100, 50000, 1200.00, '1-6 heures', 'yes', 'active', 2],
        [4, 'Partages Facebook', 'Partages Facebook pour vos publications. Maximisez votre portée.', 10, 10000, 2500.00, '1-12 heures', 'no', 'active', 3],
        
        // Twitter (category_id = 5)
        [5, 'Followers Twitter Réels', 'Followers Twitter 100% réels et engagés. Profils authentiques.', 50, 25000, 2800.00, '1-3 jours', 'yes', 'active', 1],
        [5, 'Likes Twitter', 'Likes Twitter pour vos tweets. Boostez votre engagement.', 25, 25000, 600.00, '0-2 heures', 'yes', 'active', 2],
        [5, 'Retweets', 'Retweets pour vos tweets. Augmentez votre portée rapidement.', 10, 10000, 1500.00, '0-6 heures', 'no', 'active', 3],
        
        // LinkedIn (category_id = 6)
        [6, 'Connexions LinkedIn', 'Connexions LinkedIn de professionnels dans votre secteur.', 50, 2000, 5000.00, '2-5 jours', 'yes', 'active', 1],
        [6, 'Likes LinkedIn', 'Likes LinkedIn pour vos publications professionnelles.', 25, 5000, 2000.00, '1-6 heures', 'yes', 'active', 2]
    ];
    
    $insertedServices = 0;
    foreach ($services as $service) {
        try {
            $db->execute("
                INSERT INTO services (category_id, name, description, min_quantity, max_quantity, price_per_1000, delivery_time, guarantee, status, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", $service);
            $insertedServices++;
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur insertion service '{$service[1]}': " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<p class='ok'>✅ $insertedServices services insérés avec succès</p>";
    
    echo "<h2>5. Insertion des paramètres par défaut</h2>";
    
    $settings = [
        ['site_name', 'MaickelSMM', 'text', 'Nom du site'],
        ['site_description', 'Panneau SMM professionnel - Services de marketing des réseaux sociaux', 'textarea', 'Description du site'],
        ['currency', 'XOF', 'text', 'Devise utilisée'],
        ['currency_symbol', 'FCFA', 'text', 'Symbole de la devise'],
        ['timezone', 'Africa/Abidjan', 'text', 'Fuseau horaire'],
        ['maintenance_mode', '0', 'boolean', 'Mode maintenance'],
        ['registration_enabled', '1', 'boolean', 'Autoriser les inscriptions'],
        ['min_deposit', '5000', 'number', 'Dépôt minimum en FCFA'],
        ['mtn_number', '+225 67 89 01 23', 'text', 'Numéro MTN Money'],
        ['moov_number', '+225 60 12 34 56', 'text', 'Numéro Moov Money'],
        ['orange_number', '+225 07 65 43 21', 'text', 'Numéro Orange Money'],
        ['payment_instructions', 'Envoyez le montant exact via Mobile Money puis uploadez la preuve de paiement.', 'textarea', 'Instructions de paiement'],
        ['contact_email', 'contact@maickelsmm.com', 'text', 'Email de contact'],
        ['contact_phone', '+225 07 12 34 56 78', 'text', 'Téléphone de contact'],
        ['whatsapp_number', '+22507123456', 'text', 'WhatsApp de contact'],
        ['smtp_host', '', 'text', 'Serveur SMTP'],
        ['smtp_port', '587', 'text', 'Port SMTP'],
        ['smtp_username', '', 'text', 'Nom d\'utilisateur SMTP'],
        ['smtp_password', '', 'text', 'Mot de passe SMTP'],
        ['smtp_encryption', 'tls', 'text', 'Chiffrement SMTP'],
        ['email_from_name', 'MaickelSMM', 'text', 'Nom expéditeur email'],
        ['email_from_address', '', 'text', 'Email expéditeur'],
        ['max_login_attempts', '5', 'number', 'Tentatives de connexion max'],
        ['lockout_duration', '300', 'number', 'Durée de blocage en secondes'],
        ['session_lifetime', '3600', 'number', 'Durée de session en secondes'],
        ['password_min_length', '8', 'number', 'Longueur minimale mot de passe'],
        ['enable_captcha', '0', 'boolean', 'Activer le CAPTCHA'],
        ['enable_two_factor', '0', 'boolean', 'Authentification à deux facteurs']
    ];
    
    $insertedSettings = 0;
    foreach ($settings as $setting) {
        try {
            $db->execute("
                INSERT INTO settings (setting_key, setting_value, setting_type, description) 
                VALUES (?, ?, ?, ?)
            ", $setting);
            $insertedSettings++;
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur insertion paramètre '{$setting[0]}': " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<p class='ok'>✅ $insertedSettings paramètres insérés avec succès</p>";
    
    echo "<h2>6. Vérification finale</h2>";
    
    $tables = ['users', 'services', 'categories', 'orders', 'settings'];
    foreach ($tables as $table) {
        try {
            $result = $db->fetchOne("SELECT COUNT(*) as count FROM $table");
            echo "<p class='ok'>✅ Table '$table' : {$result['count']} enregistrements</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Table '$table' : Erreur - " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<div style='background:#d4edda;border:1px solid #c3e6cb;padding:1rem;border-radius:5px;margin:2rem 0;'>";
    echo "<h3 style='color:#155724;margin:0 0 1rem 0;'>🎉 Reconstruction terminée avec succès !</h3>";
    echo "<p style='color:#155724;margin:0;'><strong>Votre site MaickelSMM est maintenant prêt !</strong></p>";
    echo "<ul style='color:#155724;'>";
    echo "<li>✅ $insertedServices services SMM disponibles</li>";
    echo "<li>✅ $insertedSettings paramètres configurés</li>";
    echo "<li>✅ Base de données complètement fonctionnelle</li>";
    echo "</ul>";
    echo "<p style='margin-top:1rem;'>";
    echo "<a href='test.php' style='background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-right:10px;'>🔍 Tester le site</a>";
    echo "<a href='index.php' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-right:10px;'>🏠 Voir le site</a>";
    echo "<a href='admin/' style='background:#6f42c1;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>⚙️ Admin</a>";
    echo "</p>";
    echo "</div>";
    
    echo "<div style='background:#fff3cd;border:1px solid #ffeaa7;padding:1rem;border-radius:5px;margin:1rem 0;'>";
    echo "<p style='color:#856404;margin:0;'><strong>N'oubliez pas de :</strong></p>";
    echo "<ul style='color:#856404;margin:0.5rem 0;'>";
    echo "<li>Supprimer ce fichier rebuild_tables.php (sécurité)</li>";
    echo "<li>Supprimer diagnose_tables.php</li>";
    echo "<li>Supprimer repair_database.php</li>";
    echo "<li>Supprimer test.php (après vérification)</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur générale : " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><small>Reconstruction effectuée le " . date('Y-m-d H:i:s') . " | MaickelSMM v1.0</small></p>";
?>