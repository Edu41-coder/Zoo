<?php
require_once (__DIR__ . '/../classes/Database.php');
require_once (__DIR__ . '/../classes/Avis.php');

$pseudo = $_POST['pseudoAvis'];
$avis = $_POST['messageAvis'];

// Utilisation de la classe Avis pour ajouter un avis
$database = Database::getInstance();
$db = $database->connect();
$avisObj = new Avis($db);
if ($avisObj->ajouterAvis($pseudo, $avis)) {
    header('Location: ../html/formulaire_contact.php?message=okAvis');
} else {
    header('Location: ../html/formulaire_contact.php?message=avisKo');
}
exit();
?>