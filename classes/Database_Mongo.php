<?php
require_once(__DIR__ . '/../vendor/autoload.php'); // Inclure l'autoloader de Composer

use MongoDB\Client;

if (!class_exists('Database_Mongo')) {
    class Database_Mongo {
        private $host;
        private $db_name;
        private $client;
        private $bdd;

        // Maintenir l'instance de la classe
        private static $instance = null;

        // Le constructeur accepte des arguments pour les paramètres de connexion
        private function __construct($host = 'mongodb://localhost:27017', $db_name = 'zoo4') {
            $this->host = $host;
            $this->db_name = $db_name;
            $this->client = new Client($this->host);
            $this->bdd = $this->client->{$this->db_name};
        }

        // L'objet est créé de l'intérieur de la classe elle-même
        // seulement si la classe n'a pas d'instance
        public static function getInstance($host = 'mongodb://localhost:27017', $db_name = 'zoo4') {
            if (self::$instance == null) {
                self::$instance = new Database_Mongo($host, $db_name);
            }

            return self::$instance;
        }

        public function getBdd() {
            return $this->bdd;
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