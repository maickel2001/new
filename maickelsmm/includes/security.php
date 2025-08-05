<?php
/**
 * Système de sécurité - MaickelSMM
 * Protection contre les attaques courantes
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

// Empêcher l'accès direct
if (!defined('ROOT_PATH')) {
    die('Accès direct interdit');
}

/**
 * Classe de sécurité
 */
class Security {
    
    /**
     * Générer un token CSRF
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Vérifier un token CSRF
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Obtenir le champ CSRF pour les formulaires
     */
    public static function getCSRFField() {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Valider un fichier uploadé
     */
    public static function validateUploadedFile($file, $allowedTypes = [], $maxSize = MAX_FILE_SIZE) {
        // Vérifier si le fichier a été uploadé
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('Fichier non valide');
        }
        
        // Vérifier la taille
        if ($file['size'] > $maxSize) {
            throw new Exception('Fichier trop volumineux (max ' . self::formatBytes($maxSize) . ')');
        }
        
        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedTypes) && !in_array($extension, $allowedTypes)) {
            throw new Exception('Type de fichier non autorisé. Types acceptés : ' . implode(', ', $allowedTypes));
        }
        
        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf'
        ];
        
        if (isset($allowedMimes[$extension]) && $mimeType !== $allowedMimes[$extension]) {
            throw new Exception('Type MIME non valide');
        }
        
        // Vérifier si c'est une image valide (pour les images)
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                throw new Exception('Fichier image corrompu');
            }
            
            // Limiter la taille des images
            if ($imageInfo[0] > 5000 || $imageInfo[1] > 5000) {
                throw new Exception('Image trop grande (max 5000x5000 pixels)');
            }
        }
        
        return true;
    }
    
    /**
     * Nettoyer un nom de fichier
     */
    public static function sanitizeFilename($filename) {
        // Supprimer les caractères dangereux
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Limiter la longueur
        if (strlen($filename) > 100) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($name, 0, 100 - strlen($extension) - 1) . '.' . $extension;
        }
        
        return $filename;
    }
    
    /**
     * Vérifier la force d'un mot de passe
     */
    public static function checkPasswordStrength($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une minuscule';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial';
        }
        
        // Vérifier contre les mots de passe courants
        $commonPasswords = [
            'password', '123456', '12345678', 'qwerty', 'abc123',
            'password123', 'admin', 'letmein', 'welcome', 'monkey'
        ];
        
        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = 'Ce mot de passe est trop courant';
        }
        
        return $errors;
    }
    
    /**
     * Limiter le taux de requêtes
     */
    public static function rateLimitCheck($key, $maxAttempts = 5, $timeWindow = 300) {
        $sessionKey = 'rate_limit_' . md5($key . getClientIP());
        
        if (!isset($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = [
                'count' => 1,
                'first_attempt' => time()
            ];
            return true;
        }
        
        $data = $_SESSION[$sessionKey];
        
        // Réinitialiser si la fenêtre de temps est expirée
        if (time() - $data['first_attempt'] > $timeWindow) {
            $_SESSION[$sessionKey] = [
                'count' => 1,
                'first_attempt' => time()
            ];
            return true;
        }
        
        // Vérifier le nombre de tentatives
        if ($data['count'] >= $maxAttempts) {
            return false;
        }
        
        // Incrémenter le compteur
        $_SESSION[$sessionKey]['count']++;
        return true;
    }
    
    /**
     * Bloquer une IP suspecte
     */
    public static function blockSuspiciousIP($reason = 'Activité suspecte') {
        $ip = getClientIP();
        $sessionKey = 'blocked_ip_' . md5($ip);
        
        $_SESSION[$sessionKey] = [
            'blocked_at' => time(),
            'reason' => $reason
        ];
        
        // Logger l'événement
        error_log("IP bloquée: $ip - Raison: $reason");
        
        // Répondre avec un 403
        http_response_code(403);
        die('Accès interdit');
    }
    
    /**
     * Vérifier si une IP est bloquée
     */
    public static function isIPBlocked() {
        $ip = getClientIP();
        $sessionKey = 'blocked_ip_' . md5($ip);
        
        if (isset($_SESSION[$sessionKey])) {
            $blockData = $_SESSION[$sessionKey];
            
            // Débloquer après 1 heure
            if (time() - $blockData['blocked_at'] > 3600) {
                unset($_SESSION[$sessionKey]);
                return false;
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Détecter les tentatives d'injection SQL
     */
    public static function detectSQLInjection($input) {
        $patterns = [
            '/(\s|^)(union|select|insert|update|delete|drop|create|alter|exec|execute)(\s|$)/i',
            '/(\s|^)(or|and)(\s|$)(1|true)(\s|$)(=|!=)(\s|$)(1|true)/i',
            '/(\s|^)(\'|"|\`|\-\-|\/\*|\*\/)/i',
            '/(0x[0-9a-f]+)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Détecter les tentatives XSS
     */
    public static function detectXSS($input) {
        $patterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/<object[^>]*>.*?<\/object>/is',
            '/<embed[^>]*>/i',
            '/javascript:/i',
            '/on\w+\s*=/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Nettoyer les données d'entrée de manière agressive
     */
    public static function deepClean($data) {
        if (is_array($data)) {
            return array_map([self::class, 'deepClean'], $data);
        }
        
        // Supprimer les caractères de contrôle
        $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $data);
        
        // Supprimer les scripts
        $data = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $data);
        
        // Supprimer les iframes
        $data = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $data);
        
        // Supprimer les objets
        $data = preg_replace('/<object[^>]*>.*?<\/object>/is', '', $data);
        
        // Supprimer les embeds
        $data = preg_replace('/<embed[^>]*>/i', '', $data);
        
        // Nettoyer les attributs JavaScript
        $data = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $data);
        
        // Supprimer javascript:
        $data = preg_replace('/javascript:/i', '', $data);
        
        return trim($data);
    }
    
    /**
     * Valider une URL
     */
    public static function validateURL($url) {
        // Vérifier le format de base
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Vérifier le schéma
        $allowedSchemes = ['http', 'https'];
        $scheme = parse_url($url, PHP_URL_SCHEME);
        
        if (!in_array($scheme, $allowedSchemes)) {
            return false;
        }
        
        // Vérifier l'hôte
        $host = parse_url($url, PHP_URL_HOST);
        if (empty($host)) {
            return false;
        }
        
        // Bloquer les IPs locales
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Générer un nom de fichier sécurisé
     */
    public static function generateSecureFilename($originalName, $prefix = '') {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        
        return $prefix . $timestamp . '_' . $random . '.' . $extension;
    }
    
    /**
     * Vérifier les headers HTTP suspects
     */
    public static function checkSuspiciousHeaders() {
        $suspiciousHeaders = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP'
        ];
        
        foreach ($suspiciousHeaders as $header) {
            if (isset($_SERVER[$header])) {
                $value = $_SERVER[$header];
                
                // Vérifier les IPs multiples (possible proxy)
                if (strpos($value, ',') !== false) {
                    error_log("Possible proxy détecté: $header = $value");
                }
                
                // Vérifier les IPs privées
                $ips = explode(',', $value);
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                        error_log("IP privée détectée: $ip");
                    }
                }
            }
        }
    }
    
    /**
     * Logger les événements de sécurité
     */
    public static function logSecurityEvent($event, $details = []) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'event' => $event,
            'details' => $details,
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? ''
        ];
        
        $logMessage = json_encode($logData);
        error_log("SECURITY: $logMessage");
        
        // Stocker en base de données si nécessaire
        try {
            $db = Database::getInstance();
            $db->execute(
                "INSERT INTO security_logs (event, ip_address, user_agent, details, created_at) VALUES (?, ?, ?, ?, NOW())",
                [$event, $logData['ip'], $logData['user_agent'], json_encode($details)]
            );
        } catch (Exception $e) {
            // Ignorer les erreurs de base de données pour les logs de sécurité
        }
    }
    
    /**
     * Formater les octets
     */
    private static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

/**
 * Middleware de sécurité - À appeler au début de chaque requête
 */
function securityMiddleware() {
    // Vérifier si l'IP est bloquée
    if (Security::isIPBlocked()) {
        http_response_code(403);
        die('Accès interdit');
    }
    
    // Vérifier les headers suspects
    Security::checkSuspiciousHeaders();
    
    // Vérifier les tentatives d'injection dans les paramètres GET
    foreach ($_GET as $key => $value) {
        if (Security::detectSQLInjection($value) || Security::detectXSS($value)) {
            Security::logSecurityEvent('injection_attempt', [
                'type' => 'GET',
                'parameter' => $key,
                'value' => $value
            ]);
            Security::blockSuspiciousIP('Tentative d\'injection détectée');
        }
    }
    
    // Vérifier les tentatives d'injection dans les paramètres POST
    foreach ($_POST as $key => $value) {
        if (is_string($value) && (Security::detectSQLInjection($value) || Security::detectXSS($value))) {
            Security::logSecurityEvent('injection_attempt', [
                'type' => 'POST',
                'parameter' => $key,
                'value' => substr($value, 0, 100) // Limiter la longueur du log
            ]);
            Security::blockSuspiciousIP('Tentative d\'injection détectée');
        }
    }
    
    // Limiter le taux de requêtes global
    if (!Security::rateLimitCheck('global', 100, 60)) { // 100 requêtes par minute
        Security::logSecurityEvent('rate_limit_exceeded', ['limit' => 'global']);
        http_response_code(429);
        die('Trop de requêtes');
    }
}

// Appeler le middleware de sécurité
securityMiddleware();

?>