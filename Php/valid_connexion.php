<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../helpers.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/User.php');

$pseudo = $_POST['pseudo'] ?? '';
$password = $_POST['password'] ?? '';

// Log pour vérifier les données POST
error_log("Pseudo: $pseudo, Password: $password", 3, __DIR__ . '/../logs/php-error.log');

// Utilisation de la classe Database pour se connecter à la base de données
$database = Database::getInstance(); // Utilisation du singleton
$db = $database->connect();

// Vérifiez si la connexion à la base de données est réussie
if ($db === null) {
    error_log("Erreur de connexion à la base de données", 3, __DIR__ . '/../logs/php-error.log');
    header('Location: ../html/connexion.php?message=connexionKo');
    exit();
}

// Utilisation de la classe User pour gérer les utilisateurs
$user = new User($db);

if ($user->verifyPassword($pseudo, $password)) {
    $_SESSION['utilisateur'] = $pseudo;
    header('Location: ../index.php?message=connexionOk');
} else {
    error_log("Échec de la vérification du mot de passe pour l'utilisateur: $pseudo", 3, __DIR__ . '/../logs/php-error.log');
    header('Location: ../html/connexion.php?message=connexionKo');
    exit();
}
?>