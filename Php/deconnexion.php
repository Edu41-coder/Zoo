<?php
require_once(__DIR__ . '/../helpers.php'); // Mise à jour du chemin vers helpers.php

session_start(); // Démarre la session
session_unset(); // Supprime toutes les variables de session
session_destroy(); // Détruit la session

header('Location: ../index.php?message=deconnexionOk'); // Redirige vers la page d'accueil avec un message de déconnexion
exit(); // Termine le script
?>