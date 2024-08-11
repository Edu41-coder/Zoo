<?php
require_once(__DIR__ . '/../Php/verification_connexion.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/Animal.php');

if ($role != "admin") {
    header('Location: ../html/liste_habitat.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // On récupère les données depuis le formulaire
    $id = $_POST["id"];
    $prenom = $_POST["prenom"];
    $race = $_POST["race"];
    $habitat = $_POST["habitat"];

    // Connexion à la base de données via le singleton
    $database = Database::getInstance();
    $db = $database->connect();
    $imageHandler = new ImageHandler($db);
    $image=new Image($db);
    $animal = new Animal($db, $imageHandler,$image);

    // Mise à jour de l'animal
    $success = $animal->updateAnimal($id, $prenom, $race, $habitat);
    if ($success) {
        header('Location: ../html/liste_habitat.php?message=modifAnimalOk');
    } else {
        header('Location: ../html/liste_habitat.php?message=modifAnimalFail');
    }
    exit();
} else {
    // Debugging: Afficher les valeurs des paramètres GET
    echo "ID: " . htmlspecialchars($_GET["id"]) . "<br>";
    echo "Prénom: " . htmlspecialchars($_GET["prenom"]) . "<br>";
    echo "État: " . htmlspecialchars($_GET["etat"]) . "<br>";
    echo "Race: " . htmlspecialchars($_GET["race"]) . "<br>";
    echo "Habitat: " . htmlspecialchars($_GET["habitat"]) . "<br>";
}
?>