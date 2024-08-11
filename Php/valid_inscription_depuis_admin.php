<?php
require(__DIR__ . '/verification_connexion.php');
require_once(__DIR__ . '/../helpers.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/User.php');

$pseudo = $_POST['pseudo'];
$password = $_POST['password'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$role = $_POST['role'];

// Vérification que le pseudo est une adresse e-mail valide
if (!filter_var($pseudo, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../html/monCompte.php?message=erreur4');
    exit();
}

if ($role == 1 || $role == 4) {
    // Le rôle est bon

    // Vérification de la longueur du mot de passe, lettre majuscule et caractère spécial
    if (strlen($password) >= 9 && preg_match('/[A-Z]/', $password) && preg_match('/[\W]/', $password)) {
        try {
            // Utilisation de la classe Database pour se connecter à la base de données
            $database = Database::getInstance();
            $db= $database->connect();

            // Utilisation de la classe User pour gérer les utilisateurs
            $user = new User($db);

            // Vérification si l'utilisateur existe déjà
            $existingUser = $user->getUserByUsername($pseudo);
            if ($existingUser) {
                echo "Debug: Utilisateur existant trouvé: " . print_r($existingUser, true);
                header('Location: ../html/monCompte.php?message=erreur1');
                exit();
            } else {
                // Création de l'utilisateur
                if ($user->createUser($pseudo, $password, $nom, $prenom, $role)) {
                    $mailDelivreur = "hehermosilla@gmail.com";
                    $nom = "ZOO Arcadia";
                    $subject = "Inscription au ZOO Arcadia";
                    $message = "Bienvenue sur le ZOO Arcadia !\n\n
                                Votre nom d'utilisateur est votre email.\n\n
                                Rapprochez-vous de l'administrateur du site pour obtenir votre mot de passe.";
                    $headers = [
                        'MIME-Version' => '1.0',
                        'Content-type' => 'text/plain; charset=UTF-8',
                        'From' => "{$nom} <{$mailDelivreur}>",
                        'X-Mailer' => 'PHP/' . phpversion(),
                    ];
                    $mailEnvoi = mail($pseudo, $subject, $message, $headers);

                    header('Location: ../html/monCompte.php?message=ok');
                    exit();
                } else {
                    header('Location: ../html/monCompte.php?message=erreur1');
                    exit();
                }
            }
        } catch (PDOException $e) {
            echo 'Erreur de connexion : ' . $e->getMessage();
        }
    } else {
        header('Location: ../html/monCompte.php?message=erreur2');
        exit();
    }
} else {
    header('Location: ../html/monCompte.php?message=erreur3');
    exit();
}
?>