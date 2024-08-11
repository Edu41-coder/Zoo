<?php
require_once(__DIR__ . '/../Php/verification_connexion.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/Avis.php');

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $avis_id = $_POST['avis_id'];

    // Obtenir la connexion à la base de données singleton
    $database = Database::getInstance();
    $db = $database->connect();

    // Utiliser la classe Avis pour supprimer l'avis
    $avis = new Avis($db);

    // Exécuter la suppression et rediriger en fonction du résultat
    if ($avis->deleteAvis($avis_id)) {
        header('Location: ../html/monCompte.php?message=suppressionOk');
    } else {
        header('Location: ../html/monCompte.php?message=suppressionFail');
    }
    exit();
}
?>