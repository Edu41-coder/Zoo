<?php
require_once(__DIR__ . '/Database.php'); // Inclure la classe Database

class Zoo {
    private $conn;

    // Constructeur
    public function __construct(PDO $conn) {
        $this->conn = $conn ;
    }

    // Obtenir les heures d'ouverture du zoo
    public function getOpeningHours() {
        $requete = "SELECT donnee FROM settings WHERE nom = 'ouvertureZoo'";
        $stmt = $this->conn->query($requete);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['donnee'] : null;
    }

    // Obtenir les heures de fermeture du zoo
    public function getClosingHours() {
        $requete = "SELECT donnee FROM settings WHERE nom = 'fermetureZoo'";
        $stmt = $this->conn->query($requete);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['donnee'] : null;
    }
}
?>