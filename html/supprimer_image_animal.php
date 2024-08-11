<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supprimer une image de l'animal</title>
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
    require_once (__DIR__ . '/../classes/Animal.php');
    require_once (__DIR__ . '/../classes/ImageHandler.php');
    require_once (__DIR__ . '/../classes/Image.php');

    // Récupération de l'instance unique de la connexion à la base de données
    $database = Database::getInstance();
    $db = $database->connect();
    // Création de l'objet Animal avec la connexion à la base de données
    $imageHandler = new ImageHandler($db);
    $image = new Image($db);
    $animal = new Animal($db, $imageHandler, $image);

    $page = 'habitat';
    include (__DIR__ . '/../templates/menu.php');

    // Vérification du rôle de l'utilisateur
    if ($role != "admin") {
        header('Location: ../html/liste_habitat.php');
        exit();
    }

    // Traitement du formulaire de suppression d'image
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $image_id = (int) $_POST['image_id']; // Convertir en entier
            if ($image->deleteAnimalImage($image_id)) {
                header('Location: detail_animal.php?id=' . $_POST['id'] . '&message=suppressionOk');
            } else {
                throw new Exception('Erreur lors de la suppression de l\'image.');
            }
            exit();
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
        }
    }

    // Récupération des images de l'animal
    $animal_id = (int) $_GET['id']; // Convertir en entier
    $resultatImage = $animal->getAnimalImages($animal_id);
    ?>
    <div class="container">
        <h1>Supprimer une image de l'animal <?php echo htmlspecialchars($_GET['nom']); ?></h1>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
            <div class="form-group">
                <label for="imageSelect">Sélectionner une image à supprimer</label>
                <select class="form-control" id="imageSelect" name="image_id" required>
                    <?php foreach ($resultatImage as $image): ?>
                        <option value="<?php echo htmlspecialchars($image['image_id']); ?>">
                            <?php echo htmlspecialchars($image['image_data']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-danger mt10perso">Supprimer</button>
            <button class="btn btn-primary mt10perso"><a class="lien"
                    href="detail_animal.php?id=<?php echo htmlspecialchars($_GET['id']); ?>">Retour sur la fiche de
                    l'animal</a></button>
        </form>
    </div>
    <?php include (__DIR__ . '/../templates/footer.php'); ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>