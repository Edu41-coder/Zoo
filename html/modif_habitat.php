<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un habitat</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
        // Vérification de la connexion
        require_once(__DIR__ . '/../Php/verification_connexion.php');
        $page = 'habitat';
        // Inclusion du menu
        include(__DIR__ . '/../templates/menu.php');
        // Inclusion des classes nécessaires
        require_once(__DIR__ . '/../classes/Database.php');
        require_once(__DIR__ . '/../classes/Habitat.php');

        // Récupération de l'instance unique de la base de données
        $database = Database::getInstance();
        $db = $database->connect();

        // Création d'une instance de la classe Habitat
        $imageHandler = new ImageHandler($db);
        $image=new Image($db);
        $habitat = new Habitat($db, $imageHandler,$image);

        // Récupération des données GET
        $id = $_GET['id'];
        $nom = $_GET['nom'];
        $description = $_GET['description'];
        $commentaire = $_GET['commentaire'];

        // Vérification du rôle de l'utilisateur
        if ($role != "admin") {
            header('Location: liste_habitat.php');
            exit();
        }
    ?>
    <div class="container">
        <h1>Modifier un habitat</h1>
        <form action="../Php/modif-habitat.php" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group mt10perso">
                <label for="nomHabitat">Nom</label>
                <input type="text" class="form-control" id="nomHabitat" name="nom" value="<?php echo $nom; ?>" required>
            </div>
            <div class="form-group mt10perso">
                <label for="descriptionHabitat">Description</label>
                <input type="text" class="form-control" id="descriptionHabitat" name="description" value="<?php echo $description; ?>" required>
            </div>
            <button type="submit" class="btn btn-success mt10perso">Modifier</button>
            <button class="btn btn-primary mt10perso"><a class="lien" href="liste_habitat.php">Retour à la liste des habitats</a></button>
        </form>
    </div>
    <?php
        // Inclusion du pied de page
        include(__DIR__ . '/../templates/footer.php');
    ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>