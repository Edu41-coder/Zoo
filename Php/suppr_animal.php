<?php
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/Database_Mongo.php'); // Inclure la classe Database_Mongo
require_once(__DIR__ . '/../classes/Animal.php');

// Obtenir la connexion à la base de données MySQL via le singleton
$database = Database::getInstance();
$bdd = $database->connect();

// Créer une instance de la classe Animal
$imageHandler = new ImageHandler($bdd);
$image = new Image($bdd);
$animal = new Animal($bdd, $imageHandler, $image);

$animal_id = $_GET['id'] ?? null;

if ($animal_id) {
    error_log("Animal ID: $animal_id"); // Log l'ID de l'animal

    // Récupérer l'image de l'animal
    $animalData = $animal->getAnimalById($animal_id);
    $imagePath = $animalData['image_data'] ?? null;

    if ($animalData === false) {
        error_log("Erreur : Impossible de récupérer les données de l'animal avec l'ID $animal_id");
    } else {
        error_log("Données de l'animal récupérées : " . print_r($animalData, true));
    }

    // Supprimer l'animal de la base de données
    if ($animal->deleteAnimal($animal_id)) {
        error_log("Animal supprimé de la base de données : $animal_id");

        // Supprimer l'image du dossier uploads
        if ($imagePath && file_exists(__DIR__ . '/../image/uploads/' . $imagePath)) {
            if (unlink(__DIR__ . '/../image/uploads/' . $imagePath)) {
                error_log("Image supprimée : $imagePath");
            } else {
                error_log("Erreur : Impossible de supprimer l'image : $imagePath");
            }
        } else {
            error_log("Aucune image à supprimer ou fichier non trouvé : $imagePath");
        }
        header('Location: ../html/liste_habitat.php?message=supprAnimalOk');
    } else {
        error_log("Erreur : Échec de la suppression de l'animal avec l'ID $animal_id");
        // Ajout d'un log pour obtenir plus de détails sur l'erreur
        error_log("Erreur SQL : " . print_r($bdd->errorInfo(), true));
        header('Location: ../html/liste_habitat.php?message=supprAnimalFail');
    }
} else {
    error_log("Erreur : Aucun ID d'animal fourni");
    header('Location: ../html/liste_habitat.php?message=supprAnimalFail');
}
exit();