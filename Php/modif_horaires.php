<?php
require_once(__DIR__ . '/../helpers.php');
require_once(__DIR__ . '/../classes/Database.php'); // Inclusion de la classe Database

$page = 'compte';

$type = $_POST["typeForm"];

if ($type == "horaires") {
    $debut = $_POST["heureDebut"];
    $fin = $_POST["heureFin"];

    // Utilisation de la classe Database pour se connecter à la base de données
    $database = Database::getInstance(); // Utilisation du singleton
    $bdd = $database->connect();

    // Préparation et exécution des requêtes pour mettre à jour les horaires
    $requete = "UPDATE settings SET donnee = :debut WHERE nom = 'ouvertureZoo'";
    $requete2 = "UPDATE settings SET donnee = :fin WHERE nom = 'fermetureZoo'";
    $requetePrepare = $bdd->prepare($requete);
    $requetePrepare->bindParam(':debut', $debut);
    $requetePrepare->execute();
    $requetePrepare2 = $bdd->prepare($requete2);
    $requetePrepare2->bindParam(':fin', $fin);
    $requetePrepare2->execute();

    // Redirection après succès de la mise à jour
    header('Location: ../html/monCompte.php?message=modifHorairesOk');
    exit();
} else {
    // Redirection en cas d'erreur dans l'envoi du formulaire
    header('Location: ../html/monCompte.php?message=erreurDansEnvoiFormulaire');
    exit();
}
?>