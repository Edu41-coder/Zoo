<?php
require_once(__DIR__ . '/verification_connexion.php');
require_once(__DIR__ . '/../classes/Database.php'); // Inclure la classe Database
require_once(__DIR__ . '/../classes/Avis.php');

// Vérifier le rôle de l'utilisateur
if ($role != "employe") {
    header('Location: liste_service.php');
    exit();
}
$database = Database::getInstance();
$db = $database->connect();

// Récupérer l'ID de l'avis à valider
$id = $_GET["id"];

// Utilisation de la classe Avis pour valider un avis
$avisObj = new Avis($db);
if ($avisObj->validerAvis($id)) {
    header('Location: ../html/monCompte.php?message=avisValide');
} else {
    header('Location: ../html/monCompte.php?message=avisKo');
}
exit();
?>