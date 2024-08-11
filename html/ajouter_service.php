<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un service</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
        // Vérification de la connexion
        require_once(__DIR__ . '/../Php/verification_connexion.php');
        // Inclusion des classes nécessaires
        require_once(__DIR__ . '/../classes/Database.php');
        require_once(__DIR__ . '/../classes/Service.php');

        // Obtenir la connexion à la base de données via le singleton
        $database = Database::getInstance();
        $db = $database->connect();
        $service = new Service($db);

        $page = 'service';
        include(__DIR__ . '/../templates/menu.php'); // Inclusion du menu

        // Vérification du rôle de l'utilisateur
        if ($role != "admin") {
            header('Location: liste_service.php');
            exit();
        }

        // Traitement du formulaire d'ajout de service
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $service->addService($_POST['nom'], $_POST['description']);
                echo "<div class='alert alert-success'>Service ajouté avec succès.</div>";
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
            }
        }
    ?>
    <div class="container">
        <h1>Ajouter un service</h1>
        <form action="" method="post">
            <div class="form-group mt10perso">
                <label for="nomService">Nom</label>
                <input type="text" class="form-control" id="nomService" name="nom" placeholder="Nom du service" required>
            </div>
            <div class="form-group mt10perso">
                <label for="descriptionService">Description</label>
                <input type="text" class="form-control" id="descriptionService" name="description" placeholder="Description du service" required>
            </div>
            <button type="submit" class="btn btn-success mt10perso">Ajouter</button>
            <button class="btn btn-primary mt10perso"><a class="lien" href="liste_service.php">Retour à la liste des services</a></button>
        </form>
    </div>
    <?php include(__DIR__ . '/../templates/footer.php'); ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>