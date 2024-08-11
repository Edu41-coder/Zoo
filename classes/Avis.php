<?php
require_once(__DIR__ . '/Database.php'); // Inclure la classe Database

class Avis
{
    private PDO $conn; // Connexion à la base de données
    private string $table = 'avis'; // Nom de la table des avis

    // Le constructeur accepte un argument pour la connexion à la base de données
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Méthode pour ajouter un avis
    public function ajouterAvis(string $pseudo, string $commentaire): bool
    {
        $requete = 'INSERT INTO ' . $this->table . ' (pseudo, commentaire, isvisible) VALUES (:pseudo, :commentaire, FALSE)';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $stmt->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Méthode pour valider un avis
    public function validerAvis(int $id): bool
    {
        $requete = 'UPDATE ' . $this->table . ' SET isvisible = 1 WHERE avis_id = :id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Méthode pour obtenir les avis non validés
    public function obtenirAvisNonValides(): array
    {
        $requete = 'SELECT * FROM ' . $this->table . ' WHERE isvisible = 0';
        $stmt = $this->conn->prepare($requete);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour obtenir les derniers avis validés
    public function getLatestAvis(int $limit = 5): array
    {
        $requete = 'SELECT * FROM ' . $this->table . ' WHERE isvisible > 0 ORDER BY avis_id DESC LIMIT :limit';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour supprimer un avis
    public function deleteAvis(int $id): bool
    {
        $requete = 'DELETE FROM ' . $this->table . ' WHERE avis_id = :id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>