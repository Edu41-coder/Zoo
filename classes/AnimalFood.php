<?php
require_once(__DIR__ . '/Database.php'); // Inclure la classe Database

class AnimalFood
{
    private PDO $conn;
    private string $table = 'nourriture_animal';

    // Le constructeur accepte une connexion PDO en argument
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getFoodByAnimalId(int $animal_id): array
    {
        $requete = "SELECT * FROM " . $this->table . " WHERE animal_id = :id ORDER BY date DESC, heure DESC";
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addFood(int $animal_id, string $nourriture, int $quantite, string $date, string $heure): bool
    {
        $requete = 'INSERT INTO ' . $this->table . ' (date, nourriture, heure, quantite, animal_id) VALUES (:date, :nourriture, :heure, :quantite, :animal_id)';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':nourriture', $nourriture, PDO::PARAM_STR);
        $stmt->bindParam(':heure', $heure, PDO::PARAM_STR);
        $stmt->bindParam(':quantite', $quantite, PDO::PARAM_INT);
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>