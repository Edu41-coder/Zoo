<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Écrire un commentaire sur un habitat</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    // Vérification de la connexion
    require_once (__DIR__ . '/../Php/verification_connexion.php');
    // Inclusion des classes nécessaires
    require_once (__DIR__ . '/../classes/Database.php');
    require_once (__DIR__ . '/../classes/Habitat.php');

    // Récupération de l'instance unique de la base de données
    $database = Database::getInstance();
    $db = $database->connect();
    // Création de l'objet Habitat avec la connexion à la base de données
    $imageHandler = new ImageHandler($db);
    $image = new Image($db);
    $habitat = new Habitat($db, $imageHandler, $image);
    $page = 'habitat';
    // Inclusion du menu
    include (__DIR__ . '/../templates/menu.php');

    // Récupération des paramètres GET
    $id = $_GET['id'];
    $nom = $_GET['nom'];

    // Vérification du rôle de l'utilisateur
    if ($role != "veto") {
        header('Location: liste_habitat.php');
        exit();
    }

    // Récupération du commentaire existant
    $commentaireExistant = $habitat->getComment($id)['commentaire_habitat'];
    ?>
    <div class="container">
        <h1>Écrire un commentaire sur l'habitat <?php echo htmlspecialchars($nom); ?></h1>
        <form action="../Php/ecrire-commentaire-habitat.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <div class="form-group mt10perso">
                <label for="commentaireHabitat">Commentaire sur l'habitat</label>
                <textarea type="text" class="form-control" id="commentaireHabitat" name="commentaire"
                    placeholder="Commentaire sur l'habitat"
                    required><?php echo htmlspecialchars($commentaireExistant); ?></textarea>
            </div>
            <button type="submit" class="btn btn-success mt10perso">Écrire un commentaire</button>
            <button class="btn btn-primary mt10perso"><a class="lien" href="liste_habitat.php">Retour à la liste des
                    habitats</a></button>
        </form>
    </div>
    <?php include (__DIR__ . '/../templates/footer.php'); ?>
    <!-- Bootstrap Bundle avec Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>