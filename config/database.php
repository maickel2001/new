<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'cree2gk_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuration du site
define('SITE_NAME', 'CREE 2GK');
define('SITE_URL', 'http://localhost/cree2gk');
define('ADMIN_EMAIL', 'admin@cree2gk.com');

// Configuration de sécurité
define('JWT_SECRET', 'your-secret-key-here-change-in-production');
define('ENCRYPTION_KEY', 'your-encryption-key-here-change-in-production');

// Configuration des paiements
define('KIAPAY_PUBLIC_KEY', 'your-kiapay-public-key');
define('KIAPAY_PRIVATE_KEY', 'your-kiapay-private-key');
define('PAYPAL_CLIENT_ID', 'your-paypal-client-id');
define('PAYPAL_CLIENT_SECRET', 'your-paypal-client-secret');

// Configuration email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');

// Timezone
date_default_timezone_set('Europe/Paris');

// Connexion à la base de données
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Fonction utilitaire pour obtenir la connexion
function getDB() {
    return Database::getInstance()->getConnection();
}
?>