<?php
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/Database_Mongo.php'); // Inclure la classe Database_Mongo
require_once(__DIR__ . '/../classes/Habitat.php');
require_once(__DIR__ . '/../classes/Animal.php');

// Obtenir la connexion à la base de données singleton
$database = Database::getInstance();
$db = $database->connect();

if ($db === null) {
    die("Erreur de connexion à la base de données.");
}

// Obtenir la connexion à la base de données MongoDB via le singleton
$databaseMongo = Database_Mongo::getInstance();
$dbMongo = $databaseMongo->getBdd();
$collection = $dbMongo->selectCollection('consultationParAnimal'); // Nom de la collection MongoDB

require_once(__DIR__ . '/../classes/ImageHandler.php');
$imageHandler = new ImageHandler($db);
$image = new Image($db);
$habitat = new Habitat($db, $imageHandler, $image);
$animal = new Animal($db, $imageHandler, $image, $collection);

$habitat_id = $_GET['id'] ?? null;

if ($habitat_id) {
    // Vérifier s'il y a des animaux dans l'habitat
    $animalsInHabitat = $animal->getAnimalsByHabitat($habitat_id);
    if (!empty($animalsInHabitat)) {
        // Rediriger avec un message d'erreur
        header('Location: ../html/liste_habitat.php?message=habitatNotEmpty');
        exit();
    }

    // Procéder à la suppression de l'habitat
    if ($habitat->deleteHabitat($habitat_id)) {
        header('Location: ../html/liste_habitat.php?message=supprOk');
    } else {
        header('Location: ../html/liste_habitat.php?message=supprFail');
    }
} else {
    header('Location: ../html/liste_habitat.php?message=supprFail');
}
exit();