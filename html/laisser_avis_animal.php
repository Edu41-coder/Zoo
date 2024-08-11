<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un avis sur un animal</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
        // Inclusion des fichiers nécessaires
        require_once(__DIR__ . '/../Php/verification_connexion.php');
        require_once(__DIR__ . '/../classes/Database.php');
        require_once(__DIR__ . '/../classes/VeterinaryReport.php');


        // Obtenir la connexion à la base de données via le singleton
        $database = Database::getInstance();
        $db = $database->connect();
        $report = new VeterinaryReport($db);

        $page = 'habitat';
        $prenom = $_GET['prenom'];
        $id = $_GET['id'];
        include(__DIR__ . '/../templates/menu.php'); // Inclusion du menu

        // Vérifier le rôle de l'utilisateur
        if ($role != "veto") {
            header('Location: liste_habitat.php');
            exit();
        }

        // Traitement du formulaire d'ajout d'avis
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $report->addReview($_POST['id'], $_POST['detailAvis'], $_POST['etatActuel']);
                echo "<div class='alert alert-success'>Avis ajouté avec succès.</div>";
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
            }
        }
    ?>
    <div class="container">
        <h1>Ajouter un avis pour l'animal <?php echo htmlspecialchars($prenom); ?></h1>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <div class="form-group mt10perso">
                <label for="detailAvis">Détail</label>
                <input type="text" class="form-control" id="detailAvis" name="detailAvis" placeholder="Détail de l'avis" required>
            </div>
            <div class="form-group mt10perso">
                <label for="etatActuel">État actuel de l'animal</label>
                <input type="text" class="form-control" id="etatActuel" name="etatActuel" placeholder="État actuel de l'animal" required>
            </div>
            <button type="submit" class="btn btn-success mt10perso">Ajouter</button>
            <button class="btn btn-primary mt10perso"><a class="lien" href="./liste_habitat.php">Retour à la liste des habitats</a></button>
        </form>
    </div>
    <?php include(__DIR__ . '/../templates/footer.php'); ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>