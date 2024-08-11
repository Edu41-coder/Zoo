<?php
require_once(__DIR__ . '/ImageHandler.php');
require_once(__DIR__ . '/Database.php'); // Inclure la classe Database
require_once(__DIR__ . '/Image.php'); // Inclure la classe Image

class Habitat
{
    private PDO $conn; // Connexion à la base de données
    private string $table = 'habitat'; // Nom de la table des habitats
    private string $imageTable = 'assoimage_habitat'; // Nom de la table d'association image-habitat
    private ImageHandler $imageHandler; // Instance de la classe ImageHandler
    private Image $image; // Instance de la classe Image

    // Le constructeur accepte des arguments pour la connexion à la base de données, ImageHandler et Image
    public function __construct(PDO $conn, ImageHandler $imageHandler, Image $image)
    {
        $this->conn = $conn;
        $this->imageHandler = $imageHandler;
        $this->image = $image;
    }

    // Méthode pour obtenir les images associées à un habitat
    public function getHabitatImages(int $habitat_id): array
    {
        return $this->image->getHabitatImages($habitat_id);
    }

    // Méthode pour ajouter une image à un habitat
    public function addHabitatImage(int $habitat_id, array $image): bool
    {
        $uploadFileDir = realpath(__DIR__ . '/../image/uploads') . '/';
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        // Vérifier la taille de l'image
        ImageHandler::checkImageSize($image, $maxFileSize);

        // Vérifier si l'image existe déjà dans la base de données par son nom
        if ($this->imageHandler->imageExistsByName($image['name'])) {
            throw new Exception('Cette image existe déjà.');
        }

        $fileName = ImageHandler::uploadImage($image, $uploadFileDir);

        try {
            $this->conn->beginTransaction();

            // Insérer l'image dans la table image
            $stmt = $this->conn->prepare("INSERT INTO image (image_data) VALUES (:image_data)");
            $stmt->bindParam(':image_data', $fileName, PDO::PARAM_STR);
            $stmt->execute();
            $image_id = $this->conn->lastInsertId();

            // Associer l'image à l'habitat dans la table d'association
            $stmt = $this->conn->prepare("INSERT INTO " . $this->imageTable . " (habitat_id, image_id) VALUES (:habitat_id, :image_id)");
            $stmt->bindParam(':habitat_id', $habitat_id, PDO::PARAM_INT);
            $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception('Erreur lors de l\'ajout de l\'image à l\'habitat : ' . $e->getMessage());
        }
    }

    // Méthode pour supprimer une image associée à un habitat
    public function deleteHabitatImage(int $image_id): bool
    {
        return $this->image->deleteHabitatImage($image_id);
    }

    // Méthode pour supprimer un habitat
    public function deleteHabitat(int $id): bool
    {
        // Vérifier s'il y a des animaux dans l'habitat
        $requete3 = 'SELECT COUNT(animal.prenom) AS nb FROM animal WHERE habitat_id= :id';
        $requetePrepare3 = $this->conn->prepare($requete3);
        $requetePrepare3->bindParam(':id', $id, PDO::PARAM_INT);
        $requetePrepare3->execute();
        $resultats = $requetePrepare3->fetch(PDO::FETCH_ASSOC);

        if ($resultats["nb"] > 0) {
            return false; // Ne pas supprimer l'habitat s'il y a des animaux
        } else {
            try {
                $this->conn->beginTransaction();

                // Récupérer les images associées à l'habitat
                $images = $this->image->getHabitatImages($id);

                // Supprimer les associations image-habitat
                $stmt = $this->conn->prepare('DELETE FROM ' . $this->imageTable . ' WHERE habitat_id = :id');
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                // Supprimer les images de la table image et les fichiers d'images du dossier uploads
                foreach ($images as $image) {
                    $stmt = $this->conn->prepare('DELETE FROM image WHERE image_id = :image_id');
                    $stmt->bindParam(':image_id', $image['image_id'], PDO::PARAM_INT);
                    $stmt->execute();

                    $filePath = __DIR__ . '/../image/uploads/' . $image['image_data'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                // Supprimer l'habitat
                $stmt = $this->conn->prepare('DELETE FROM ' . $this->table . ' WHERE habitat_id = :id');
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                $this->conn->commit();
                return true;
            } catch (Exception $e) {
                $this->conn->rollBack();
                throw new Exception('Erreur lors de la suppression de l\'habitat : ' . $e->getMessage());
            }
        }
    }

    // Méthode pour ajouter un habitat avec une image
    public function addHabitat(string $nom, string $description, array $image): bool
    {
        $uploadFileDir = realpath(__DIR__ . '/../image/uploads') . '/';
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        // Vérifier la taille de l'image
        ImageHandler::checkImageSize($image, $maxFileSize);

        // Vérifier si l'image existe déjà dans la base de données par son nom
        if ($this->imageHandler->imageExistsByName($image['name'])) {
            throw new Exception('Cette image existe déjà.');
        }

        $fileName = ImageHandler::uploadImage($image, $uploadFileDir);

        try {
            $this->conn->beginTransaction();

            // Insérer l'habitat dans la table habitat
            $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (nom, description) VALUES (:nom, :description)");
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->execute();
            $habitat_id = $this->conn->lastInsertId();

            // Insérer l'image dans la table image
            $stmt = $this->conn->prepare("INSERT INTO image (image_data) VALUES (:image)");
            $stmt->bindParam(':image', $fileName, PDO::PARAM_STR);
            $stmt->execute();
            $image_id = $this->conn->lastInsertId();

            // Associer l'image à l'habitat dans la table d'association
            $stmt = $this->conn->prepare("INSERT INTO " . $this->imageTable . " (habitat_id, image_id) VALUES (:habitat_id, :image_id)");
            $stmt->bindParam(':habitat_id', $habitat_id, PDO::PARAM_INT);
            $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
            $stmt->execute();

            // Valider la transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->conn->rollBack();
            throw new Exception('Erreur lors de l\'ajout de l\'habitat et de l\'image : ' . $e->getMessage());
        }
    }

    // Méthode pour obtenir les détails d'un habitat
    public function getHabitatDetails(int $habitat_id): array
    {
        $requete = 'SELECT * FROM ' . $this->table . ' WHERE habitat_id = :id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $habitat_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // Méthode pour obtenir tous les habitats, triés par nom
    public function getAllHabitats(): array
    {
        $requete = 'SELECT * FROM ' . $this->table . ' ORDER BY nom ASC';
        $stmt = $this->conn->prepare($requete);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour obtenir un nombre limité d'habitats
    public function getAllHabitatsLim(int $limit = 15): array
    {
        $requete = 'SELECT * FROM ' . $this->table . ' LIMIT :limit';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour convertir un code d'erreur de téléchargement en message
    public function codeToMessage(int $code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return "Le fichier téléchargé dépasse la directive upload_max_filesize dans php.ini.";
            case UPLOAD_ERR_FORM_SIZE:
                return "Le fichier téléchargé dépasse la directive MAX_FILE_SIZE spécifiée dans le formulaire HTML.";
            case UPLOAD_ERR_PARTIAL:
                return "Le fichier n'a été que partiellement téléchargé.";
            case UPLOAD_ERR_NO_FILE:
                return "Aucun fichier n'a été téléchargé.";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Un dossier temporaire est manquant.";
            case UPLOAD_ERR_CANT_WRITE:
                return "Échec de l'écriture du fichier sur le disque.";
            case UPLOAD_ERR_EXTENSION:
                return "Une extension PHP a arrêté le téléchargement du fichier.";
            default:
                return "Erreur inconnue lors du téléchargement du fichier.";
        }
    }

    // Méthode pour obtenir le commentaire d'un habitat
    public function getComment(int $habitat_id): array
    {
        $requete = 'SELECT commentaire_habitat FROM habitat WHERE habitat_id = :habitat_id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':habitat_id', $habitat_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // Méthode pour mettre à jour le commentaire d'un habitat
    public function updateComment(int $habitat_id, string $comment): bool
    {
        $requete = 'UPDATE habitat SET commentaire_habitat = :comment WHERE habitat_id = :habitat_id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':habitat_id', $habitat_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Méthode pour mettre à jour les détails d'un habitat
    public function updateHabitat(int $id, string $nom, string $description, string $commentaire): bool
    {
        $requete = 'UPDATE ' . $this->table . ' SET nom = :nom, description = :description, commentaire_habitat = :commentaire WHERE habitat_id = :id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
?>