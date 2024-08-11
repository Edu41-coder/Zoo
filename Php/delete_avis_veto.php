<?php
require_once(__DIR__ . '/../Php/verification_connexion.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/VeterinaryReport.php');

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $avis_id = $_POST['avis_id'];

    // Obtenir la connexion à la base de données singleton
    $database = Database::getInstance();
    $db = $database->connect();

    // Utiliser la classe VeterinaryReport pour supprimer l'avis
    $report = new VeterinaryReport($db);

    // Exécuter la suppression et rediriger en fonction du résultat
    if ($report->deleteReview($avis_id)) {
        header('Location: ../html/avis_animal.php?message=suppressionOk');
    } else {
        header('Location: ../html/avis_animal.php?message=suppressionFail');
    }
    exit();
}
?>