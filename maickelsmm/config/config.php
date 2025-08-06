<?php
/**
 * Configuration principale - MaickelSMM
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration générale
define('SITE_NAME', 'MaickelSMM');
define('SITE_VERSION', '1.0.0');
define('SITE_URL', 'http://localhost/maickelsmm');
define('ADMIN_EMAIL', 'admin@maickelsmm.com');

// Chemins
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PAGES_PATH', ROOT_PATH . '/pages');
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/assets/uploads');

// URLs
define('BASE_URL', SITE_URL);
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_URL', BASE_URL . '/assets/uploads');
define('ADMIN_URL', BASE_URL . '/admin');

// Configuration de sécurité
define('HASH_ALGO', 'sha256');
define('ENCRYPTION_KEY', 'MaickelSMM_2024_SecureKey_!@#$%^&*');
define('SESSION_LIFETIME', 3600 * 24); // 24 heures
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Configuration des uploads
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('PAYMENT_PROOF_PATH', UPLOADS_PATH . '/payments');
define('BANNERS_PATH', UPLOADS_PATH . '/banners');

// Configuration email
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_ENCRYPTION', 'tls');

// Paramètres par défaut
define('DEFAULT_CURRENCY', 'XOF');
define('DEFAULT_CURRENCY_SYMBOL', 'FCFA');
define('DEFAULT_TIMEZONE', 'Africa/Abidjan');
define('DEFAULT_LANGUAGE', 'fr');

// Messages flash
define('MSG_SUCCESS', 'success');
define('MSG_ERROR', 'error');
define('MSG_WARNING', 'warning');
define('MSG_INFO', 'info');

// Statuts des commandes
define('ORDER_PENDING', 'pending');
define('ORDER_PROCESSING', 'processing');
define('ORDER_COMPLETED', 'completed');
define('ORDER_CANCELLED', 'cancelled');
define('ORDER_REFUNDED', 'refunded');

// Rôles utilisateurs
define('ROLE_USER', 'user');
define('ROLE_ADMIN', 'admin');
define('ROLE_SUPERADMIN', 'superadmin');

// Configuration de pagination
define('ITEMS_PER_PAGE', 20);
define('ADMIN_ITEMS_PER_PAGE', 50);

// Configuration de cache
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 heure

/**
 * Fonction pour charger les paramètres depuis la base de données
 */
function loadSettings() {
    try {
        require_once INCLUDES_PATH . '/functions.php';
        $settings = getSettings();
        
        foreach ($settings as $key => $value) {
            $constant_name = strtoupper($key);
            if (!defined($constant_name)) {
                define($constant_name, $value);
            }
        }
    } catch (Exception $e) {
        error_log("Erreur lors du chargement des paramètres: " . $e->getMessage());
    }
}

/**
 * Fonction pour définir le fuseau horaire
 */
function setTimezone($timezone = DEFAULT_TIMEZONE) {
    date_default_timezone_set($timezone);
}

/**
 * Fonction pour obtenir l'URL de base
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . rtrim($path, '/');
}

/**
 * Fonction pour rediriger
 */
function redirect($url, $permanent = false) {
    if (!headers_sent()) {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
        exit();
    } else {
        echo '<script>window.location.href="' . $url . '";</script>';
        exit();
    }
}

/**
 * Fonction pour afficher les messages flash
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Fonction pour récupérer et supprimer les messages flash
 */
function getFlashMessages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Fonction pour nettoyer les données d'entrée
 */
function cleanInput($data) {
    if (is_array($data)) {
        return array_map('cleanInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction pour valider un email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Fonction pour générer un token sécurisé
 */
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Fonction pour hasher un mot de passe
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Fonction pour vérifier un mot de passe
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Fonction pour obtenir l'adresse IP du client
 */
function getClientIP() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Fonction pour formater un prix
 */
function formatPrice($price, $currency = DEFAULT_CURRENCY_SYMBOL) {
    return number_format($price, 0, ',', ' ') . ' ' . $currency;
}

/**
 * Fonction pour formater une date
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Fonction pour générer un slug
 */
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Fonction pour tronquer un texte
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// Initialisation
setTimezone();

// Gestion des erreurs en production
if (!defined('DEBUG') || !DEBUG) {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Configuration de session sécurisée
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Régénérer l'ID de session périodiquement
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Charger les paramètres de la base de données
// loadSettings(); // Décommenté après la création des fonctions

?>