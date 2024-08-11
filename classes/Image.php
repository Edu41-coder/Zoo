<?php
require_once(__DIR__ . '/Database.php'); // Inclure la classe Database

class Image
{
    private PDO $conn;
    private string $tableAnimal = 'assoimage_animal';
    private string $tableHabitat = 'assoimage_habitat';

    // Le constructeur accepte un argument pour la connexion à la base de données
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Récupérer les images associées à un animal
    public function getAnimalImages(int $animal_id): array
    {
        $requete = "SELECT image.image_data, image.image_id 
                    FROM " . $this->tableAnimal . " 
                    LEFT JOIN image ON assoimage_animal.image_id = image.image_id
                    WHERE assoimage_animal.animal_id = :id";
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($result === false) {
            throw new Exception('Erreur lors de la récupération des images de l\'animal.');
        }

        return $result;
    }

    // Récupérer les images associées à un habitat
    public function getHabitatImages(int $habitat_id): array
    {
        $requete = "SELECT image.image_data, image.image_id 
                    FROM " . $this->tableHabitat . " 
                    LEFT JOIN image ON assoimage_habitat.image_id = image.image_id
                    WHERE assoimage_habitat.habitat_id = :id";
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $habitat_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($result === false) {
            throw new Exception('Erreur lors de la récupération des images de l\'habitat.');
        }

        return $result;
    }

    // Récupérer une image associée à un animal
    public function getAnimalImage(int $animal_id): array
    {
        $requete = "SELECT image.image_data 
                    FROM " . $this->tableAnimal . " 
                    LEFT JOIN image ON assoimage_animal.image_id = image.image_id
                    WHERE assoimage_animal.animal_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result === false) {
            throw new Exception('Erreur lors de la récupération de l\'image de l\'animal.');
        }

        return $result ?: [];
    }

    // Supprimer une image associée à un animal
    public function deleteAnimalImage(int $image_id): bool
    {
        try {
            $this->conn->beginTransaction();

            // Récupérer le nom de l'image
            $stmt = $this->conn->prepare('SELECT image_data FROM image WHERE image_id = :image_id');
            $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
            $stmt->execute();
            $image = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($image) {
                $filePath = __DIR__ . '/../image/uploads/' . $image['image_data'];

                // Supprimer l'association image-animal
                $stmt = $this->conn->prepare('DELETE FROM ' . $this->tableAnimal . ' WHERE image_id = :image_id');
                $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
                $stmt->execute();

                // Supprimer l'image de la table image
                $stmt = $this->conn->prepare('DELETE FROM image WHERE image_id = :image_id');
                $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
                $stmt->execute();

                // Supprimer le fichier image du dossier uploads
                if (file_exists($filePath)) {
                    if (!unlink($filePath)) {
                        throw new Exception('Erreur lors de la suppression du fichier image.');
                    }
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception('Erreur lors de la suppression de l\'image : ' . $e->getMessage());
        }
    }

    // Supprimer une image associée à un habitat
    public function deleteHabitatImage(int $image_id): bool
    {
        try {
            $this->conn->beginTransaction();

            // Récupérer le nom de l'image
            $stmt = $this->conn->prepare('SELECT image_data FROM image WHERE image_id = :image_id');
            $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
            $stmt->execute();
            $image = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($image) {
                $filePath = __DIR__ . '/../image/uploads/' . $image['image_data'];

                // Supprimer l'association image-habitat
                $stmt = $this->conn->prepare('DELETE FROM ' . $this->tableHabitat . ' WHERE image_id = :image_id');
                $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
                $stmt->execute();

                // Supprimer l'image de la table image
                $stmt = $this->conn->prepare('DELETE FROM image WHERE image_id = :image_id');
                $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
                $stmt->execute();

                // Supprimer le fichier image du dossier uploads
                if (file_exists($filePath)) {
                    if (!unlink($filePath)) {
                        throw new Exception('Erreur lors de la suppression du fichier image.');
                    }
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception('Erreur lors de la suppression de l\'image : ' . $e->getMessage());
        }
    }
}
?>