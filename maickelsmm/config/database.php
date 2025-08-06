<?php
/**
 * Configuration de la base de données - MaickelSMM
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

// Configuration de la base de données - Modifiez ces valeurs selon votre hébergeur
// Pour Hostinger, utilisez les informations de votre panneau de contrôle
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'maickelsmm');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// Classe de connexion à la base de données
class Database {
    private static $instance = null;
    private $connection;
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;

    /**
     * Constructeur privé pour le pattern Singleton
     */
    private function __construct() {
        $this->connect();
    }

    /**
     * Méthode pour obtenir l'instance unique de la base de données
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Connexion à la base de données
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}",
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_TIMEOUT => 30
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            // Log l'erreur sans exposer les détails sensibles
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            
            // En production, afficher une erreur générique
            if (getenv('ENVIRONMENT') !== 'development') {
                die("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
            } else {
                die("Erreur de connexion à la base de données: " . $e->getMessage());
            }
        }
    }

    /**
     * Obtenir la connexion PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Exécuter une requête préparée
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erreur d'exécution de requête: " . $e->getMessage());
            throw new Exception("Erreur lors de l'exécution de la requête");
        }
    }

    /**
     * Récupérer une seule ligne
     */
    public function fetchOne($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->fetch();
    }

    /**
     * Récupérer toutes les lignes
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }

    /**
     * Récupérer le dernier ID inséré
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Compter le nombre de lignes affectées
     */
    public function rowCount($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->rowCount();
    }

    /**
     * Démarrer une transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Valider une transaction
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * Annuler une transaction
     */
    public function rollback() {
        return $this->connection->rollback();
    }

    /**
     * Vérifier si une transaction est active
     */
    public function inTransaction() {
        return $this->connection->inTransaction();
    }

    /**
     * Échapper une chaîne pour éviter les injections SQL (utiliser de préférence les requêtes préparées)
     */
    public function quote($string) {
        return $this->connection->quote($string);
    }

    /**
     * Tester la connexion
     */
    public function testConnection() {
        try {
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}

// Tester la connexion au chargement (seulement en développement)
if (getenv('ENVIRONMENT') === 'development') {
    try {
        $db = Database::getInstance();
        if (!$db->testConnection()) {
            error_log("Test de connexion à la base de données échoué");
        }
    } catch (Exception $e) {
        error_log("Erreur lors du test de connexion: " . $e->getMessage());
    }
}
?>