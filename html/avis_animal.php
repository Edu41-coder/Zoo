<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zoo Arcadia</title>
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
    require_once (__DIR__ . '/../classes/Database_Mongo.php'); // Inclure la classe Database_Mongo
    require_once (__DIR__ . '/../classes/VeterinaryReport.php');
    require_once (__DIR__ . '/../classes/Animal.php'); // Inclure la classe Animal
    
    // Obtenir la connexion à la base de données MySQL via le singleton
    $database = Database::getInstance();
    $db = $database->connect();

    // Instancier les classes avec la connexion à la base de données
    $report = new VeterinaryReport($db);
    $imageHandler = new ImageHandler($db);
    $image = new Image($db);
    $animal = new Animal($db, $imageHandler, $image);

    $page = 'habitat';
    include (__DIR__ . '/../templates/menu.php'); // Inclusion du menu
    
    // Rediriger si l'utilisateur n'est pas un administrateur
    if ($role != "admin") {
        header('Location: liste_habitat.php');
        exit();
    }

    // Récupérer les paramètres GET
    $id = $_GET['id'] ?? 0;
    $debut = $_GET['debut'] ?? '';
    $fin = $_GET['fin'] ?? '';
    $message = $_GET['message'] ?? '';

    // Récupérer les rapports et les animaux
    $resultats = $report->getReports($id, $debut, $fin);
    $animals = $animal->getAnimals(); // Appeler getAnimals sur l'instance Animal
    setlocale(LC_TIME, 'fr_FR.UTF-8');
    ?>
    <div class="container">
        <h1>Avis du vétérinaire pour l'animal</h1>

        <?php if ($message == 'suppressionOk'): ?>
            <div class="alert alert-success">Avis supprimé avec succès.</div>
        <?php elseif ($message == 'suppressionFail'): ?>
            <div class="alert alert-danger">Échec de la suppression de l'avis.</div>
        <?php endif; ?>

        <div id="filtres" class="mt10perso">
            <form action="avis_animal.php" method="get">
                <select name="id">
                    <option value="0">Tous les animaux</option>
                    <?php
                    foreach ($animals as $animal) {
                        $selectedAnimal = ($id == $animal["animal_id"]) ? "selected" : "";
                        echo ('<option ' . $selectedAnimal . ' value="' . $animal["animal_id"] . '">' . $animal["prenom"] . '</option>');
                    }
                    ?>
                </select>
                <input type="date" name="debut" placeholder="Date de début"
                    value="<?php echo htmlspecialchars($debut); ?>">
                <input type="date" name="fin" placeholder="Date de fin" value="<?php echo htmlspecialchars($fin); ?>">
                <button type="submit" class="btn btn-success">Chercher</button>
                <button type="button" class="btn btn-primary"><a class="lien"
                        href="avis_animal.php?id=<?php echo htmlspecialchars($id); ?>">Réinitialiser le
                        filtre</a></button>
            </form>
        </div>

        <div class="divDebut">
            <button type="button" class="btn btn-success btn-lg"><a class="lien" href="liste_habitat.php">Retour à la
                    liste des habitats</a></button>
        </div>
        <?php
        if (count($resultats) == 0) {
            echo '<p class="mt10perso">Aucun avis du vétérinaire pour cet animal.</p>';
        } else {
            foreach ($resultats as $ligne) {
                $dateFormatee = strftime('%e %B %Y', strtotime($ligne['date']));
                echo '<h2 class="mt30perso">Avis du vétérinaire du ' . $dateFormatee . ' pour ' . htmlspecialchars($ligne['prenom']) . '</h2>';
                echo '<p class="mt10perso"><span class="boldSpan">État : </span>' . htmlspecialchars($ligne['etatAvis']) . '</p>';
                echo '<p><span class="boldSpan">Détail de l\'état : </span>' . htmlspecialchars($ligne['detail']) . '</p>';
                echo '<form action="../Php/delete_avis_veto.php" method="post" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer cet avis ?\');">';
                echo '<input type="hidden" name="avis_id" value="' . htmlspecialchars($ligne['rapport_veterinaire_id']) . '">';
                echo '<button type="submit" class="btn btn-danger">Supprimer</button>';
                echo '</form>';
            }
        }
        ?>
    </div>
    <?php include (__DIR__ . '/../templates/footer.php'); ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>