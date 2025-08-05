<?php
/**
 * Système d'authentification - MaickelSMM
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/config.php';

/**
 * Classe d'authentification
 */
class Auth {
    private $db;
    private $max_attempts;
    private $lockout_time;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->max_attempts = MAX_LOGIN_ATTEMPTS;
        $this->lockout_time = LOGIN_LOCKOUT_TIME;
    }
    
    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register($data) {
        $errors = $this->validateRegistrationData($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            // Vérifier si l'utilisateur existe déjà
            if ($this->userExists($data['email'], $data['username'])) {
                return ['success' => false, 'errors' => ['Email ou nom d\'utilisateur déjà utilisé']];
            }
            
            // Hasher le mot de passe
            $hashedPassword = hashPassword($data['password']);
            
            // Générer un token de vérification
            $verificationToken = generateSecureToken();
            
            // Insérer l'utilisateur
            $query = "INSERT INTO users (username, email, password, first_name, last_name, phone, verification_token, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $params = [
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['first_name'],
                $data['last_name'],
                $data['phone'] ?? null,
                $verificationToken
            ];
            
            $this->db->execute($query, $params);
            $userId = $this->db->lastInsertId();
            
            // Envoyer l'email de vérification (optionnel)
            $this->sendVerificationEmail($data['email'], $verificationToken);
            
            return [
                'success' => true, 
                'user_id' => $userId,
                'message' => 'Compte créé avec succès. Vérifiez votre email pour activer votre compte.'
            ];
            
        } catch (Exception $e) {
            error_log("Erreur inscription: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Erreur lors de la création du compte']];
        }
    }
    
    /**
     * Connexion d'un utilisateur
     */
    public function login($login, $password, $remember = false) {
        // Vérifier les tentatives de connexion
        if ($this->isAccountLocked($login)) {
            return [
                'success' => false, 
                'error' => 'Compte temporairement verrouillé. Réessayez dans ' . ($this->lockout_time / 60) . ' minutes.'
            ];
        }
        
        // Rechercher l'utilisateur
        $user = $this->getUserByLogin($login);
        
        if (!$user) {
            $this->recordFailedAttempt($login);
            return ['success' => false, 'error' => 'Identifiants incorrects'];
        }
        
        // Vérifier le mot de passe
        if (!verifyPassword($password, $user['password'])) {
            $this->recordFailedAttempt($login);
            return ['success' => false, 'error' => 'Identifiants incorrects'];
        }
        
        // Vérifier le statut du compte
        if ($user['status'] !== 'active') {
            return ['success' => false, 'error' => 'Compte désactivé ou bloqué'];
        }
        
        // Connexion réussie
        $this->clearFailedAttempts($login);
        $this->createUserSession($user);
        $this->updateLastLogin($user['id']);
        
        // Gestion du "Se souvenir de moi"
        if ($remember) {
            $this->setRememberToken($user['id']);
        }
        
        return [
            'success' => true, 
            'user' => $this->sanitizeUserData($user),
            'message' => 'Connexion réussie'
        ];
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        // Supprimer le token "Se souvenir de moi"
        if (isset($_COOKIE['remember_token'])) {
            $this->clearRememberToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Détruire la session
        session_unset();
        session_destroy();
        
        return ['success' => true, 'message' => 'Déconnexion réussie'];
    }
    
    /**
     * Vérifier si l'utilisateur est connecté
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }
    
    /**
     * Obtenir l'utilisateur connecté
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $user = $this->getUserById($_SESSION['user_id']);
        return $user ? $this->sanitizeUserData($user) : null;
    }
    
    /**
     * Vérifier les permissions
     */
    public function hasPermission($required_role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $user_role = $_SESSION['user_role'];
        
        switch ($required_role) {
            case ROLE_USER:
                return in_array($user_role, [ROLE_USER, ROLE_ADMIN, ROLE_SUPERADMIN]);
            case ROLE_ADMIN:
                return in_array($user_role, [ROLE_ADMIN, ROLE_SUPERADMIN]);
            case ROLE_SUPERADMIN:
                return $user_role === ROLE_SUPERADMIN;
            default:
                return false;
        }
    }
    
    /**
     * Middleware d'authentification
     */
    public function requireAuth($required_role = ROLE_USER) {
        if (!$this->isLoggedIn()) {
            redirect('/login.php');
        }
        
        if (!$this->hasPermission($required_role)) {
            redirect('/403.php');
        }
    }
    
    /**
     * Changer le mot de passe
     */
    public function changePassword($user_id, $current_password, $new_password) {
        $user = $this->getUserById($user_id);
        
        if (!$user || !verifyPassword($current_password, $user['password'])) {
            return ['success' => false, 'error' => 'Mot de passe actuel incorrect'];
        }
        
        $errors = $this->validatePassword($new_password);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $hashedPassword = hashPassword($new_password);
        $this->db->execute("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?", 
                          [$hashedPassword, $user_id]);
        
        return ['success' => true, 'message' => 'Mot de passe modifié avec succès'];
    }
    
    /**
     * Demande de réinitialisation de mot de passe
     */
    public function requestPasswordReset($email) {
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            // Ne pas révéler si l'email existe ou non
            return ['success' => true, 'message' => 'Si cet email existe, vous recevrez un lien de réinitialisation'];
        }
        
        $resetToken = generateSecureToken();
        $resetExpires = date('Y-m-d H:i:s', time() + 3600); // 1 heure
        
        $this->db->execute("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?", 
                          [$resetToken, $resetExpires, $user['id']]);
        
        $this->sendPasswordResetEmail($user['email'], $resetToken);
        
        return ['success' => true, 'message' => 'Si cet email existe, vous recevrez un lien de réinitialisation'];
    }
    
    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword($token, $new_password) {
        $user = $this->db->fetchOne("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()", [$token]);
        
        if (!$user) {
            return ['success' => false, 'error' => 'Token invalide ou expiré'];
        }
        
        $errors = $this->validatePassword($new_password);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $hashedPassword = hashPassword($new_password);
        $this->db->execute("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL, updated_at = NOW() WHERE id = ?", 
                          [$hashedPassword, $user['id']]);
        
        return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès'];
    }
    
    /**
     * Vérifier l'email
     */
    public function verifyEmail($token) {
        $user = $this->db->fetchOne("SELECT * FROM users WHERE verification_token = ?", [$token]);
        
        if (!$user) {
            return ['success' => false, 'error' => 'Token de vérification invalide'];
        }
        
        $this->db->execute("UPDATE users SET email_verified = 1, verification_token = NULL, updated_at = NOW() WHERE id = ?", 
                          [$user['id']]);
        
        return ['success' => true, 'message' => 'Email vérifié avec succès'];
    }
    
    /**
     * Valider les données d'inscription
     */
    private function validateRegistrationData($data) {
        $errors = [];
        
        // Nom d'utilisateur
        if (empty($data['username'])) {
            $errors[] = "Nom d'utilisateur requis";
        } elseif (strlen($data['username']) < 3 || strlen($data['username']) > 50) {
            $errors[] = "Le nom d'utilisateur doit contenir entre 3 et 50 caractères";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et underscores";
        }
        
        // Email
        if (empty($data['email'])) {
            $errors[] = "Email requis";
        } elseif (!isValidEmail($data['email'])) {
            $errors[] = "Email invalide";
        }
        
        // Mot de passe
        $password_errors = $this->validatePassword($data['password'] ?? '');
        $errors = array_merge($errors, $password_errors);
        
        // Confirmation mot de passe
        if (empty($data['password_confirm'])) {
            $errors[] = "Confirmation du mot de passe requise";
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }
        
        // Prénom et nom
        if (empty($data['first_name'])) {
            $errors[] = "Prénom requis";
        }
        if (empty($data['last_name'])) {
            $errors[] = "Nom requis";
        }
        
        return $errors;
    }
    
    /**
     * Valider un mot de passe
     */
    private function validatePassword($password) {
        $errors = [];
        
        if (empty($password)) {
            $errors[] = "Mot de passe requis";
        } elseif (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule";
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule";
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre";
        }
        
        return $errors;
    }
    
    /**
     * Vérifier si un utilisateur existe
     */
    private function userExists($email, $username) {
        $count = $this->db->fetchOne("SELECT COUNT(*) as count FROM users WHERE email = ? OR username = ?", 
                                    [$email, $username]);
        return $count['count'] > 0;
    }
    
    /**
     * Obtenir un utilisateur par login (email ou username)
     */
    private function getUserByLogin($login) {
        return $this->db->fetchOne("SELECT * FROM users WHERE email = ? OR username = ?", [$login, $login]);
    }
    
    /**
     * Obtenir un utilisateur par email
     */
    private function getUserByEmail($email) {
        return $this->db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    /**
     * Obtenir un utilisateur par ID
     */
    private function getUserById($id) {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    /**
     * Créer une session utilisateur
     */
    private function createUserSession($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['login_time'] = time();
    }
    
    /**
     * Nettoyer les données utilisateur
     */
    private function sanitizeUserData($user) {
        unset($user['password'], $user['verification_token'], $user['reset_token']);
        return $user;
    }
    
    /**
     * Mettre à jour la dernière connexion
     */
    private function updateLastLogin($user_id) {
        $this->db->execute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user_id]);
    }
    
    /**
     * Vérifier si le compte est verrouillé
     */
    private function isAccountLocked($login) {
        $key = 'login_attempts_' . md5($login . getClientIP());
        
        if (!isset($_SESSION[$key])) {
            return false;
        }
        
        $attempts = $_SESSION[$key];
        
        if ($attempts['count'] >= $this->max_attempts) {
            if (time() - $attempts['last_attempt'] < $this->lockout_time) {
                return true;
            } else {
                // Réinitialiser après expiration
                unset($_SESSION[$key]);
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Enregistrer une tentative échouée
     */
    private function recordFailedAttempt($login) {
        $key = 'login_attempts_' . md5($login . getClientIP());
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'last_attempt' => 0];
        }
        
        $_SESSION[$key]['count']++;
        $_SESSION[$key]['last_attempt'] = time();
    }
    
    /**
     * Effacer les tentatives échouées
     */
    private function clearFailedAttempts($login) {
        $key = 'login_attempts_' . md5($login . getClientIP());
        unset($_SESSION[$key]);
    }
    
    /**
     * Définir un token "Se souvenir de moi"
     */
    private function setRememberToken($user_id) {
        $token = generateSecureToken();
        $expires = time() + (30 * 24 * 60 * 60); // 30 jours
        
        // Stocker le token en base (optionnel, pour plus de sécurité)
        setcookie('remember_token', $token, $expires, '/', '', false, true);
    }
    
    /**
     * Effacer le token "Se souvenir de moi"
     */
    private function clearRememberToken($token) {
        // Supprimer le token de la base si stocké
    }
    
    /**
     * Envoyer l'email de vérification
     */
    private function sendVerificationEmail($email, $token) {
        $subject = "Vérification de votre compte MaickelSMM";
        $message = "
            <h2>Vérification de votre compte</h2>
            <p>Cliquez sur le lien suivant pour vérifier votre compte :</p>
            <p><a href='" . BASE_URL . "/verify.php?token=" . $token . "'>Vérifier mon compte</a></p>
            <p>Ce lien expire dans 24 heures.</p>
        ";
        
        sendEmail($email, $subject, $message);
    }
    
    /**
     * Envoyer l'email de réinitialisation
     */
    private function sendPasswordResetEmail($email, $token) {
        $subject = "Réinitialisation de votre mot de passe MaickelSMM";
        $message = "
            <h2>Réinitialisation de mot de passe</h2>
            <p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe :</p>
            <p><a href='" . BASE_URL . "/reset-password.php?token=" . $token . "'>Réinitialiser mon mot de passe</a></p>
            <p>Ce lien expire dans 1 heure.</p>
        ";
        
        sendEmail($email, $subject, $message);
    }
}

// Instance globale
$auth = new Auth();

/**
 * Fonctions helper pour l'authentification
 */
function isLoggedIn() {
    global $auth;
    return $auth->isLoggedIn();
}

function getCurrentUser() {
    global $auth;
    return $auth->getCurrentUser();
}

function hasPermission($role) {
    global $auth;
    return $auth->hasPermission($role);
}

function requireAuth($role = ROLE_USER) {
    global $auth;
    return $auth->requireAuth($role);
}

function requireAdmin() {
    requireAuth(ROLE_ADMIN);
}

function requireSuperAdmin() {
    requireAuth(ROLE_SUPERADMIN);
}

?>