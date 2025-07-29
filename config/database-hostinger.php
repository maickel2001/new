<?php
// Configuration spécifique pour Hostinger
// Remplacez les valeurs par celles fournies dans votre hPanel

// Configuration de la base de données Hostinger
define('DB_HOST', 'localhost'); // Généralement localhost sur Hostinger
define('DB_NAME', 'u123456789_cree2gk'); // Format typique Hostinger
define('DB_USER', 'u123456789_admin'); // Votre utilisateur DB Hostinger
define('DB_PASS', 'VotreMotDePasseSecurise123!'); // Votre mot de passe DB
define('DB_CHARSET', 'utf8mb4');

// Configuration du site
define('SITE_NAME', 'CREE 2GK');
define('SITE_URL', 'https://votre-domaine.com'); // Votre domaine Hostinger
define('ADMIN_EMAIL', 'admin@votre-domaine.com');

// Configuration de sécurité (CHANGEZ CES CLÉS !)
define('JWT_SECRET', 'votre-cle-secrete-jwt-unique-et-longue-123456789');
define('ENCRYPTION_KEY', 'votre-cle-chiffrement-unique-et-longue-987654321');

// Configuration des paiements
define('KIAPAY_PUBLIC_KEY', 'pk_test_votre_cle_publique_kiapay');
define('KIAPAY_PRIVATE_KEY', 'sk_test_votre_cle_privee_kiapay');
define('PAYPAL_CLIENT_ID', 'votre_client_id_paypal');
define('PAYPAL_CLIENT_SECRET', 'votre_client_secret_paypal');

// Configuration email Hostinger
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'noreply@votre-domaine.com'); // Email créé dans hPanel
define('SMTP_PASSWORD', 'votre_mot_de_passe_email');
define('SMTP_SECURE', 'tls');

// Timezone
date_default_timezone_set('Europe/Paris');

// Configuration d'environnement
define('ENVIRONMENT', 'production'); // ou 'development'
define('DEBUG_MODE', false); // Mettre à false en production

// Configuration des erreurs pour production
if (ENVIRONMENT === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Connexion à la base de données avec gestion d'erreurs
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            // Log successful connection in development
            if (ENVIRONMENT === 'development') {
                error_log("Database connection successful");
            }
            
        } catch (PDOException $e) {
            // Log error securely
            error_log("Database connection failed: " . $e->getMessage());
            
            if (ENVIRONMENT === 'development') {
                die("Erreur de connexion à la base de données: " . $e->getMessage());
            } else {
                // En production, afficher un message générique
                die("Service temporairement indisponible. Veuillez réessayer plus tard.");
            }
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
    
    // Test de connexion
    public function testConnection() {
        try {
            $stmt = $this->connection->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            error_log("Database test failed: " . $e->getMessage());
            return false;
        }
    }
}

// Fonction utilitaire pour obtenir la connexion
function getDB() {
    return Database::getInstance()->getConnection();
}

// Fonction de test de connexion
function testDatabaseConnection() {
    return Database::getInstance()->testConnection();
}

// Fonctions utilitaires pour Hostinger
function getHostingerInfo() {
    return [
        'php_version' => phpversion(),
        'mysql_available' => extension_loaded('pdo_mysql'),
        'curl_available' => extension_loaded('curl'),
        'openssl_available' => extension_loaded('openssl'),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'
    ];
}

// Initialisation des logs si le dossier n'existe pas
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Fonction de logging personnalisée
function logMessage($message, $level = 'INFO') {
    $logFile = __DIR__ . '/../logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Test de la configuration au chargement
if (ENVIRONMENT === 'development') {
    $info = getHostingerInfo();
    logMessage("Environment info: " . json_encode($info));
}
?>