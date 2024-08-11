<?php

require_once(__DIR__ . '/verification_connexion.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/Habitat.php');

// Récupérer les données POST
$id = $_POST["id"];
$commentaire = $_POST["commentaire"];

// Obtenir la connexion à la base de données via le singleton
$database = Database::getInstance();
$bdd = $database->connect();

// Créer une instance de la classe Habitat
$imageHandler = new ImageHandler($bdd);
$image=new Image($bdd);
$habitat = new Habitat($bdd, $imageHandler,$image);

// Mettre à jour le commentaire de l'habitat
$success = $habitat->updateComment($id, $commentaire);

// Rediriger en fonction du succès de la mise à jour
if ($success) {
    header('Location: ../html/liste_habitat.php?message=commentaireOk');
} else {
    header('Location: ../html/liste_habitat.php?message=commentaireFail');
}
exit();
?>