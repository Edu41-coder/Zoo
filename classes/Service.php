<?php
require_once(__DIR__ . '/Database.php'); // Inclure la classe Database

class Service
{
    private $conn;
    private $table = 'service';

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Ajouter un service
    public function addService($nom, $description)
    {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (nom, description) VALUES (:nom, :description)");
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Obtenir les détails d'un service par ID
    public function getServiceDetails($id)
    {
        $requete = 'SELECT * FROM ' . $this->table . ' WHERE service_id = :id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour un service
    public function updateService($id, $nom, $description)
    {
        $requete = 'UPDATE ' . $this->table . ' SET nom = :nom, description = :description WHERE service_id = :id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Obtenir tous les services
    public function getAllServices()
    {
        $requete = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->query($requete);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprimer un service par ID
    public function deleteService($id)
    {
        $requete = 'DELETE FROM ' . $this->table . ' WHERE service_id = :id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>