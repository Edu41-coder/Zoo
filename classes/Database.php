<?php
// Gardes de définition de classe pour éviter la redéfinition
if (!class_exists('Database')) {
    class Database {
        private $host; // Hôte de la base de données
        private $db_name; // Nom de la base de données
        private $username; // Nom d'utilisateur pour la connexion à la base de données
        private $password; // Mot de passe pour la connexion à la base de données
        private $conn; // Connexion PDO

        // Maintenir l'instance de la classe (singleton)
        private static $instance = null;

        // Le constructeur accepte des arguments pour les paramètres de connexion
        private function __construct($host, $db_name, $username, $password) {
            $this->host = $host;
            $this->db_name = $db_name;
            $this->username = $username;
            $this->password = $password;
        }

        // Méthode pour obtenir l'instance unique de la classe (singleton)
        public static function getInstance($host = 'localhost', $db_name = 'zoo4', $username = 'pepe', $password = 'pepe') {
            if (self::$instance == null) {
                self::$instance = new Database($host, $db_name, $username, $password);
            }

            return self::$instance;
        }

        // Méthode pour établir la connexion à la base de données
        public function connect() {
            if ($this->conn === null) {
                try {
                    $this->conn = new PDO(
                        "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                        $this->username,
                        $this->password
                    );
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    echo "Erreur de connexion : " . $e->getMessage();
                }
            }
            return $this->conn;
        }

        // Empêcher l'instance d'être clonée
        private function __clone() {}

        // Empêcher l'instance d'être désérialisée
        public function __wakeup() {
            throw new Exception("Impossible de désérialiser un singleton.");
        }
    }
}
?>