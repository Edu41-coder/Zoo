<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../helpers.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/User.php');

// Utilisation de la classe Database pour se connecter à la base de données avec le pattern singleton
$database = Database::getInstance();
$bdd = $database->connect();

// Initialisation du rôle par défaut
$role = "visiteur";

if (isset($_SESSION['utilisateur'])) {
    // Utilisation de la classe User pour gérer les utilisateurs
    $user = new User($bdd);
    $userCo = $user->getUserByUsername($_SESSION['utilisateur']);

    if ($userCo) {
        if ($userCo['role_id'] == 3) {
            $role = "admin";
        } else if ($userCo['role_id'] == 1) {
            $role = "veto";
        } else if ($userCo['role_id'] == 4) {
            $role = "employe";
        }
    }
}
?>