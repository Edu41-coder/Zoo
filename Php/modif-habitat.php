<?php
require_once(__DIR__ . '/../helpers.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/Habitat.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];

    // Utilisation de la classe Database pour se connecter à la base de données avec le pattern singleton
    $database = Database::getInstance();
    $bdd = $database->connect();

    // Utilisation de la classe Habitat pour mettre à jour les informations de l'habitat
    $imageHandler = new ImageHandler($bdd);
    $image=new Image($bdd);
    $habitat = new Habitat($bdd, $imageHandler,$image);
    $success = $habitat->updateHabitat($id, $nom, $description, '');

    if ($success) {
        header('Location: ../html/liste_habitat.php?message=modifOk');
    } else {
        header('Location: ../html/liste_habitat.php?message=modifFail');
    }
    exit();
}
?>