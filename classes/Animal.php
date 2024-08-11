<?php
require_once(__DIR__ . '/ImageHandler.php');
require_once(__DIR__ . '/Database.php'); // Inclure la classe Database
require_once(__DIR__ . '/Image.php'); // Inclure la classe Image
require_once(__DIR__ . '/Database_Mongo.php'); // Inclure la classe Database_Mongo

class Animal
{
    private PDO $conn; // Connexion à la base de données
    private string $table = 'animal'; // Nom de la table des animaux
    private string $imageTable = 'assoimage_animal'; // Nom de la table d'association image-animal
    private ImageHandler $imageHandler; // Instance de la classe ImageHandler
    private Image $image; // Instance de la classe Image

    // Le constructeur accepte des arguments pour la connexion à la base de données, ImageHandler et Image
    public function __construct(PDO $conn, ImageHandler $imageHandler, Image $image)
    {
        $this->conn = $conn;
        $this->imageHandler = $imageHandler;
        $this->image = $image;
    }

    // Méthode pour obtenir les détails d'un animal
    public function getAnimalDetails(int $animal_id): array
    {
        $requete = 'SELECT animal.animal_id, animal.prenom, animal.etat, race.race_id, race.abel, habitat.habitat_id, habitat.nom 
                    FROM ' . $this->table . '
                    LEFT JOIN race ON animal.race_id = race.race_id
                    LEFT JOIN habitat ON animal.habitat_id = habitat.habitat_id
                    WHERE animal.animal_id = :id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // Méthode pour obtenir un animal par son ID
    public function getAnimalById(int $animal_id): array
    {
        $requete = 'SELECT animal.animal_id, animal.prenom, animal.etat, image.image_data 
                    FROM ' . $this->table . '
                    LEFT JOIN ' . $this->imageTable . ' ON animal.animal_id = ' . $this->imageTable . '.animal_id
                    LEFT JOIN image ON ' . $this->imageTable . '.image_id = image.image_id
                    WHERE animal.animal_id = :id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // Méthode pour supprimer un animal
    public function deleteAnimal(int $id): bool
    {
        try {
            $this->conn->beginTransaction();

            // Récupérer les images associées à l'animal
            $images = $this->image->getAnimalImages($id);

            // Supprimer les associations image-animal
            $stmt = $this->conn->prepare('DELETE FROM ' . $this->imageTable . ' WHERE animal_id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            error_log("Associations image-animal supprimées pour l'animal avec l'ID $id");

            // Supprimer les consultations par animal dans MongoDB
            $this->deleteConsultation($id);
            error_log("Consultations par animal supprimées dans MongoDB pour l'animal avec l'ID $id");

            // Supprimer l'animal
            $stmt = $this->conn->prepare('DELETE FROM ' . $this->table . ' WHERE animal_id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            error_log("Animal supprimé de la table principale pour l'ID $id");

            // Supprimer les images du dossier uploads et de la table image
            foreach ($images as $image) {
                $filePath = __DIR__ . '/../image/uploads/' . $image['image_data'];
                if (file_exists($filePath)) {
                    if (unlink($filePath)) {
                        error_log("Image supprimée : $filePath");
                    } else {
                        error_log("Erreur : Impossible de supprimer l'image : $filePath");
                    }
                } else {
                    error_log("Fichier image non trouvé : $filePath");
                }

                // Supprimer l'image de la table image
                $stmt = $this->conn->prepare('DELETE FROM image WHERE image_id = :image_id');
                $stmt->bindParam(':image_id', $image['image_id'], PDO::PARAM_INT);
                $stmt->execute();
                error_log("Image supprimée de la table image avec l'ID " . $image['image_id']);
            }

            $this->conn->commit();
            error_log("Transaction commitée avec succès pour l'animal avec l'ID $id");
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Erreur lors de la suppression de l'animal avec l'ID $id : " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour supprimer les consultations d'un animal dans MongoDB
    public function deleteConsultation(int $animal_id): void
    {
        $databaseMongo = Database_Mongo::getInstance();
        $dbMongo = $databaseMongo->getBdd();
        $collection = $dbMongo->selectCollection('consultationParAnimal');
        $collection->deleteOne(['animal_id' => $animal_id]);
    }

    // Méthode pour ajouter un animal avec une image
    public function addAnimal(string $prenom, string $etat, int $race, int $habitat, array $image): bool
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

            // Insérer l'animal
            $stmt = $this->conn->prepare("INSERT INTO " . $this->table . " (prenom, etat, race_id, habitat_id) VALUES (:prenom, :etat, :race, :habitat)");
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindParam(':etat', $etat, PDO::PARAM_STR);
            $stmt->bindParam(':race', $race, PDO::PARAM_INT);
            $stmt->bindParam(':habitat', $habitat, PDO::PARAM_INT);
            $stmt->execute();
            $animal_id = $this->conn->lastInsertId();

            // Insérer l'image
            $stmt = $this->conn->prepare("INSERT INTO image (image_data) VALUES (:image_data)");
            $stmt->bindParam(':image_data', $fileName, PDO::PARAM_STR);
            $stmt->execute();
            $image_id = $this->conn->lastInsertId();

            // Associer l'image à l'animal
            $stmt = $this->conn->prepare("INSERT INTO " . $this->imageTable . " (animal_id, image_id) VALUES (:animal_id, :image_id)");
            $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
            $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception('Erreur lors de l\'ajout de l\'animal et de l\'image : ' . $e->getMessage());
        }
    }

    // Méthode pour ajouter une image à un animal existant
    public function addImageToAnimal(int $animal_id, array $image): bool
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

            // Insérer l'image
            $stmt = $this->conn->prepare("INSERT INTO image (image_data) VALUES (:image_data)");
            $stmt->bindParam(':image_data', $fileName, PDO::PARAM_STR);
            $stmt->execute();
            $image_id = $this->conn->lastInsertId();

            // Associer l'image à l'animal
            $stmt = $this->conn->prepare("INSERT INTO " . $this->imageTable . " (animal_id, image_id) VALUES (:animal_id, :image_id)");
            $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
            $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception('Erreur lors de l\'ajout de l\'image à l\'animal : ' . $e->getMessage());
        }
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

    // Méthode pour ajouter de la nourriture à un animal
    public function addFood(int $animal_id, string $nourriture, int $quantite, string $date, string $heure): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO nourriture (animal_id, nourriture, quantite, date, heure) VALUES (:animal_id, :nourriture, :quantite, :date, :heure)");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->bindParam(':nourriture', $nourriture, PDO::PARAM_STR);
        $stmt->bindParam(':quantite', $quantite, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':heure', $heure, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Méthode pour obtenir toutes les races
    public function getRaces(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM race");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour obtenir tous les habitats
    public function getHabitats(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM habitat");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour obtenir les animaux par habitat
    public function getAnimalsByHabitat(int $habitat_id): array
    {
        $requete = 'SELECT animal.animal_id, animal.prenom, animal.etat, race.race_id, race.abel, habitat.habitat_id, habitat.nom 
                FROM ' . $this->table . '
                LEFT JOIN race ON animal.race_id = race.race_id
                LEFT JOIN habitat ON animal.habitat_id = habitat.habitat_id
                WHERE animal.habitat_id = :id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':id', $habitat_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour mettre à jour les détails d'un animal
    public function updateAnimal(int $id, string $prenom, int $race, int $habitat): bool
    {
        try {
            // Commencer une transaction
            $this->conn->beginTransaction();

            // Mise à jour dans la base de données SQL
            $requete = 'UPDATE ' . $this->table . ' SET prenom = :prenom, race_id = :race, habitat_id = :habitat WHERE animal_id = :id';
            $stmt = $this->conn->prepare($requete);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindParam(':race', $race, PDO::PARAM_INT);
            $stmt->bindParam(':habitat', $habitat, PDO::PARAM_INT);
            $stmt->execute();

            // Mise à jour dans MongoDB
            $this->updateAnimalName($id, $prenom);

            // Commit de la transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback en cas d'erreur
            $this->conn->rollBack();
            error_log("Erreur lors de la mise à jour de l'animal avec l'ID $id : " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour mettre à jour le nom d'un animal dans MongoDB
    public function updateAnimalName(int $animal_id, string $prenom): void
    {
        $databaseMongo = Database_Mongo::getInstance();
        $dbMongo = $databaseMongo->getBdd();
        $collection = $dbMongo->selectCollection('consultationParAnimal');
        $collection->updateOne(
            ['animal_id' => $animal_id],
            ['$set' => ['prenom' => $prenom]]
        );
    }

    // Méthode pour obtenir l'image d'un animal
    public function getAnimalImage(int $animal_id): array
    {
        return $this->image->getAnimalImage($animal_id);
    }

    // Méthode pour obtenir les images d'un animal
    public function getAnimalImages(int $animal_id): array
    {
        return $this->image->getAnimalImages($animal_id);
    }

    // Méthode pour supprimer une image d'un animal
    public function deleteAnimalImage(int $image_id): bool
    {
        return $this->image->deleteAnimalImage($image_id);
    }

    // Méthode pour obtenir tous les animaux
    public function getAnimals(): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour obtenir tous les animaux avec une limite
    public function getAllAnimals(int $limit = 15): array
    {
        $requete = 'SELECT animal.animal_id, animal.prenom, animal.etat, race.abel, habitat.nom 
                    FROM ' . $this->table . '
                    LEFT JOIN race ON animal.race_id = race.race_id
                    LEFT JOIN habitat ON animal.habitat_id = habitat.habitat_id
                    LIMIT :limit';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>