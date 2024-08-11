<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consulter la nourriture</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    // Inclusion des fichiers nécessaires
    require_once(__DIR__ . '/../Php/verification_connexion.php');
    require_once(__DIR__ . '/../classes/Database.php');
    require_once(__DIR__ . '/../classes/AnimalFood.php');

    // Obtenir la connexion à la base de données via le singleton
    $database = Database::getInstance();
    $db = $database->connect();
    $animalFood = new AnimalFood($db);

    $page = 'habitat';
    include(__DIR__ . '/../templates/menu.php');

    // Vérifier le rôle de l'utilisateur
    if ($role != "veto") {
        header('Location: liste_habitat.php');
        exit();
    }

    // Récupérer les paramètres de la requête
    $animal_id = $_GET['id'];
    $prenom = $_GET['prenom'];
    $resultats = $animalFood->getFoodByAnimalId($animal_id);
    setlocale(LC_TIME, 'fr_FR.UTF-8');
    ?>
    <div class="container">
        <h1>Consulter la nourriture donnée pour l'animal <?php echo htmlspecialchars($prenom); ?></h1>

        <button class="btn btn-success margin2"><a class="lien" href="liste_habitat.php">Retour à la liste des habitats</a></button>

        <?php if (count($resultats) > 0) { ?>
            <table class="table table-striped mt10perso">
                <thead>
                    <tr>
                        <th scope="col">Nourriture</th>
                        <th scope="col">Quantité en gramme</th>
                        <th scope="col">Date</th>
                        <th scope="col">Heure</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultats as $ligne) { 
                        $dateFormatee = strftime('%e %B %Y', strtotime($ligne["date"]));
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ligne["nourriture"]); ?></td>
                            <td><?php echo htmlspecialchars($ligne["quantite"]) . "g"; ?></td>
                            <td><?php echo htmlspecialchars($dateFormatee); ?></td>
                            <td><?php echo htmlspecialchars($ligne["heure"]); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="mt10perso">Cet animal n'a jamais reçu de nourriture !</div>
        <?php } ?>
    </div>
    <?php include(__DIR__ . '/../templates/footer.php'); ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>