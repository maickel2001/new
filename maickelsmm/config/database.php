<?php
/**
 * Configuration de la base de données - MaickelSMM
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'maickelsmm');
define('DB_USER', 'root');
define('DB_PASS', '');
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
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
        }
    }

    /**
     * Obtenir la connexion PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Préparer une requête
     */
    public function prepare($query) {
        return $this->connection->prepare($query);
    }

    /**
     * Exécuter une requête avec des paramètres
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
     * Obtenir un seul résultat
     */
    public function fetchOne($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->fetch();
    }

    /**
     * Obtenir tous les résultats
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }

    /**
     * Obtenir le dernier ID inséré
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Commencer une transaction
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
     * Empêcher la clonage de l'instance
     */
    private function __clone() {}

    /**
     * Empêcher la désérialisation de l'instance
     */
    private function __wakeup() {}
}

// Test de connexion
try {
    $db = Database::getInstance();
    // echo "Connexion à la base de données réussie!";
} catch (Exception $e) {
    error_log("Erreur de connexion: " . $e->getMessage());
}
?>