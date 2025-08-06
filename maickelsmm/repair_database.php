<?php
/**
 * Script de réparation de la base de données MaickelSMM
 * À exécuter une seule fois puis supprimer
 */

require_once 'config/database.php';

echo "<h1>🔧 Réparation Base de Données MaickelSMM</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;background:#f5f5f5;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";

try {
    $db = Database::getInstance();
    
    echo "<h2>1. Vérification et réparation de la table 'services'</h2>";
    
    // Vérifier si la table services existe et a la bonne structure
    try {
        $result = $db->fetchOne("DESCRIBE services");
        echo "<p class='ok'>✅ Table 'services' existe</p>";
        
        // Vérifier si elle a des données
        $count = $db->fetchOne("SELECT COUNT(*) as count FROM services");
        if ($count['count'] == 0) {
            echo "<p class='warning'>⚠️ Table 'services' vide, insertion des données...</p>";
            
            // Insérer les services par défaut
            $services = [
                // Instagram
                [1, 'Followers Instagram Réels', 'Followers Instagram 100% réels avec garantie 30 jours. Profils actifs et authentiques.', 100, 50000, 2500.00, '1-3 jours', 'yes', 1],
                [1, 'Followers Instagram Ciblés France', 'Followers Instagram ciblés géographiquement en France. Profils réels et actifs.', 50, 10000, 3500.00, '2-5 jours', 'yes', 2],
                [1, 'Likes Instagram Photo', 'Likes Instagram pour vos photos. Livraison rapide et sécurisée.', 50, 20000, 500.00, '0-1 heure', 'yes', 4],
                [1, 'Vues Instagram Stories', 'Vues pour vos stories Instagram. Augmentez votre visibilité.', 100, 50000, 300.00, '0-30 minutes', 'no', 6],
                
                // TikTok
                [2, 'Followers TikTok Réels', 'Followers TikTok 100% réels avec garantie. Profils actifs et authentiques.', 100, 100000, 1800.00, '1-3 jours', 'yes', 1],
                [2, 'Likes TikTok', 'Likes TikTok pour vos vidéos. Boost instantané de popularité.', 50, 50000, 400.00, '0-1 heure', 'yes', 3],
                [2, 'Vues TikTok Vidéos', 'Vues TikTok pour vos vidéos. Devenez viral sur TikTok.', 1000, 1000000, 150.00, '0-30 minutes', 'no', 4],
                
                // YouTube
                [3, 'Abonnés YouTube Réels', 'Abonnés YouTube 100% réels et actifs. Croissance naturelle garantie.', 50, 10000, 8000.00, '3-7 jours', 'yes', 1],
                [3, 'Vues YouTube', 'Vues YouTube pour vos vidéos. Augmentez votre visibilité rapidement.', 100, 1000000, 200.00, '0-12 heures', 'no', 2],
                [3, 'Likes YouTube', 'Likes YouTube pour vos vidéos. Boostez votre engagement.', 25, 50000, 800.00, '0-6 heures', 'yes', 3],
                
                // Facebook
                [4, 'Amis Facebook', 'Demandes d\'amis Facebook de profils réels et actifs.', 50, 5000, 3000.00, '1-3 jours', 'yes', 1],
                [4, 'Likes Page Facebook', 'Likes pour votre page Facebook. Augmentez votre crédibilité.', 100, 50000, 1200.00, '1-6 heures', 'yes', 2],
                [4, 'Partages Facebook', 'Partages Facebook pour vos publications. Maximisez votre portée.', 10, 10000, 2500.00, '1-12 heures', 'no', 4],
                
                // Twitter
                [5, 'Followers Twitter Réels', 'Followers Twitter 100% réels et engagés. Profils authentiques.', 50, 25000, 2800.00, '1-3 jours', 'yes', 1],
                [5, 'Likes Twitter', 'Likes Twitter pour vos tweets. Boostez votre engagement.', 25, 25000, 600.00, '0-2 heures', 'yes', 2],
                [5, 'Retweets', 'Retweets pour vos tweets. Augmentez votre portée rapidement.', 10, 10000, 1500.00, '0-6 heures', 'no', 3],
                
                // LinkedIn
                [6, 'Connexions LinkedIn', 'Connexions LinkedIn de professionnels dans votre secteur.', 50, 2000, 5000.00, '2-5 jours', 'yes', 1],
                [6, 'Likes LinkedIn', 'Likes LinkedIn pour vos publications professionnelles.', 25, 5000, 2000.00, '1-6 heures', 'yes', 2]
            ];
            
            $stmt = $db->execute("
                INSERT INTO services (category_id, name, description, min_quantity, max_quantity, price_per_1000, delivery_time, guarantee, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($services as $service) {
                $db->execute("
                    INSERT INTO services (category_id, name, description, min_quantity, max_quantity, price_per_1000, delivery_time, guarantee, sort_order) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ", $service);
            }
            
            echo "<p class='ok'>✅ " . count($services) . " services insérés avec succès</p>";
        } else {
            echo "<p class='ok'>✅ Table 'services' contient déjà {$count['count']} services</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur avec la table 'services': " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>2. Vérification et réparation de la table 'settings'</h2>";
    
    try {
        $result = $db->fetchOne("DESCRIBE settings");
        echo "<p class='ok'>✅ Table 'settings' existe</p>";
        
        // Vérifier si elle a des données
        $count = $db->fetchOne("SELECT COUNT(*) as count FROM settings");
        if ($count['count'] == 0) {
            echo "<p class='warning'>⚠️ Table 'settings' vide, insertion des paramètres par défaut...</p>";
            
            // Insérer les paramètres par défaut
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
                ['max_login_attempts', '5', 'number', 'Tentatives de connexion max'],
                ['lockout_duration', '300', 'number', 'Durée de blocage en secondes'],
                ['session_lifetime', '3600', 'number', 'Durée de session en secondes'],
                ['password_min_length', '8', 'number', 'Longueur minimale mot de passe'],
                ['enable_captcha', '0', 'boolean', 'Activer le CAPTCHA'],
                ['enable_two_factor', '0', 'boolean', 'Authentification à deux facteurs']
            ];
            
            foreach ($settings as $setting) {
                $db->execute("
                    INSERT INTO settings (setting_key, setting_value, setting_type, description) 
                    VALUES (?, ?, ?, ?)
                ", $setting);
            }
            
            echo "<p class='ok'>✅ " . count($settings) . " paramètres insérés avec succès</p>";
        } else {
            echo "<p class='ok'>✅ Table 'settings' contient déjà {$count['count']} paramètres</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur avec la table 'settings': " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>3. Vérification finale</h2>";
    
    // Test final de toutes les tables
    $tables = ['users', 'services', 'categories', 'orders', 'settings'];
    foreach ($tables as $table) {
        try {
            $result = $db->fetchOne("SELECT COUNT(*) as count FROM $table");
            echo "<p class='ok'>✅ Table '$table' : {$result['count']} enregistrements</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Table '$table' : Erreur - " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<div style='background:#d4edda;border:1px solid #c3e6cb;padding:1rem;border-radius:5px;margin:2rem 0;'>";
    echo "<h3 style='color:#155724;margin:0 0 1rem 0;'>🎉 Réparation terminée !</h3>";
    echo "<p style='color:#155724;margin:0;'><strong>Actions suivantes :</strong></p>";
    echo "<ul style='color:#155724;'>";
    echo "<li>Supprimez ce fichier repair_database.php</li>";
    echo "<li>Testez votre site : <a href='test.php'>test.php</a></li>";
    echo "<li>Accédez à votre site : <a href='index.php'>index.php</a></li>";
    echo "<li>Connectez-vous à l'admin : <a href='admin/'>admin/</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur générale : " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><small>Réparation effectuée le " . date('Y-m-d H:i:s') . " | MaickelSMM v1.0</small></p>";
?>