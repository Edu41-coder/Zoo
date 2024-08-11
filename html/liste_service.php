<?php
$message = $_GET['message'] ?? '';
// Page du site actuelle
$page = "service";
require_once(__DIR__ . '/../Php/verification_connexion_simple.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/Service.php');
include(__DIR__ . '/../templates/menu.php'); // Inclusion du menu

// Obtenir la connexion à la base de données singleton
$database = Database::getInstance();
$db = $database->connect();
$service = new Service($db);

// Gérer l'ajout de service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    if ($service->addService($nom, $description)) {
        header('Location: liste_service.php?message=ajoutOk');
    } else {
        header('Location: liste_service.php?message=ajoutFail');
    }
    exit();
}

// Gérer la modification de service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    if ($service->updateService($id, $nom, $description)) {
        header('Location: liste_service.php?message=modifOk');
    } else {
        header('Location: liste_service.php?message=modifFail');
    }
    exit();
}

// Gérer la suppression de service
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($service->deleteService($id)) {
        header('Location: liste_service.php?message=supprOk');
    } else {
        header('Location: liste_service.php?message=supprFail');
    }
    exit();
}

$services = $service->getAllServices();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des services</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    
    // Afficher les messages directement
    if ($message == 'ajoutOk') {
        echo "<div class='alert alert-success'>Le service a été ajouté avec succès.</div>";
    } elseif ($message == 'ajoutFail') {
        echo "<div class='alert alert-danger'>Échec de l'ajout du service.</div>";
    } elseif ($message == 'modifOk') {
        echo "<div class='alert alert-success'>Le service a été modifié avec succès.</div>";
    } elseif ($message == 'modifFail') {
        echo "<div class='alert alert-danger'>Échec de la modification du service.</div>";
    } elseif ($message == 'supprOk') {
        echo "<div class='alert alert-success'>Le service a été supprimé avec succès.</div>";
    } elseif ($message == 'supprFail') {
        echo "<div class='alert alert-danger'>Échec de la suppression du service.</div>";
    }
    ?>
    <div class="container">
        <h1>Liste des services</h1>
        <?php if ($role == "admin") { ?>
        <div class="divDebut">
            <button type="button" class="btn btn-success btn-lg"><a class="lien" href="ajouter_service.php">Ajouter un service</a></button>
        </div>
        <?php
        }

        $resultats = $service->getAllServices();

        if ($resultats) {
            ?>
            <table class="table table-striped mt10perso">
                <thead>
                    <tr>
                        <th scope="col">Nom</th>
                        <th scope="col">Description</th>
                        <?php
                        if ($role == "admin") { 
                            ?>
                            <th>Modifier</th>
                            <th>Supprimer</th>
                            <?php
                        } else if ($role == "employe") { 
                            ?>
                            <th>Modifier</th>
                            <?php
                        } 
                        ?>
                    </tr>
                </thead>
                <tbody>
            <?php

            foreach ($resultats as $ligne) {
                ?>
                <tr>
                    <td><?php echo($ligne["nom"]);  ?></td>
                    <td><?php echo($ligne["description"]);  ?></td>
                <?php
                if ($role == "admin") { 
                    ?>
                    <td><a href='modif_service.php?id=<?php echo($ligne["service_id"]); ?>&nom=<?php echo($ligne["nom"]); ?>&description=<?php echo($ligne["description"]); ?>' ><i class="fa-solid fa-pen"></i></a></td>
                    <td><a href='liste_service.php?action=delete&id=<?php echo($ligne["service_id"]); ?>' ><i class="fa-solid fa-trash"></i></a></td>
                    <?php
                } else if ($role == "employe") {
                    ?>
                    <td><a href='modif_service.php?id=<?php echo($ligne["service_id"]); ?>&nom=<?php echo($ligne["nom"]); ?>&description=<?php echo($ligne["description"]); ?>' ><i class="fa-solid fa-pen"></i></a></td>
                    <?php
                }
                ?></tr><?php
            }
            ?>
                </tbody>
            </table>
            <?php
        } else {
            echo "Erreur lors de l'exécution de la requête SQL";
        }
        ?>
    </div>
    <?php
    include(__DIR__ . '/../templates/footer.php');
    ?>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>