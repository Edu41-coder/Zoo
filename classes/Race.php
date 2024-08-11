<?php
require_once(__DIR__ . '/Database.php'); // Inclure la classe Database

class Race
{
    private $conn;
    private $table = 'race';

    // Constructeur
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Méthode pour obtenir toutes les races
    public function getAllRaces()
    {
        $requete = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->query($requete);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>