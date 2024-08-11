<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajout d'une nouvelle image pour l'animal</title>
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
        require_once(__DIR__ . '/../classes/Database_Mongo.php'); // Inclure la classe Database_Mongo
        require_once(__DIR__ . '/../classes/Animal.php');
        require_once(__DIR__ . '/../classes/ImageHandler.php');
        require_once(__DIR__ . '/../classes/Image.php');

        // Obtenir la connexion à la base de données MySQL via le singleton
        $database = Database::getInstance();
        $db = $database->connect();

        // Créer une instance de la classe Animal
        $imageHandler = new ImageHandler($db);
        $image = new Image($db);
        $animal = new Animal($db, $imageHandler, $image);

        $page = 'habitat';
        include(__DIR__ . '/../templates/menu.php'); // Inclusion du menu

        // Vérification du rôle de l'utilisateur
        if ($role != "admin") {
            header('Location: ../html/liste_habitat.php');
            exit();
        }

        // Traitement du formulaire d'ajout d'image
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $maxFileSize = 2 * 1024 * 1024; // 2MB

                // Vérifier la taille de l'image
                ImageHandler::checkImageSize($_FILES['image_id'], $maxFileSize);

                // Vérifier si l'image existe déjà dans la base de données par son nom
                if ($imageHandler->imageExistsByName($_FILES['image_id']['name'])) {
                    throw new Exception('Cette image existe déjà.');
                }

                // Ajouter l'image à l'animal
                $animal->addImageToAnimal($_POST['id'], $_FILES['image_id']);
                echo "<div class='alert alert-success'>Image ajoutée avec succès.</div>";
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
            }
        }
    ?>
    <div class="container">
        <h1>Ajouter une image pour l'animal <?php echo htmlspecialchars($_GET['nom']); ?></h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
            <div class="form-group">
                <label for="imageAnimal">Image</label>
                <div class="input-group mb-3">
                    <input type="file" class="form-control-file" id="imageAnimal" name="image_id" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt10perso">Ajouter</button>
            <button class="btn btn-primary mt10perso"><a class="lien" href="detail_animal.php?id=<?php echo htmlspecialchars($_GET['id']); ?>">Retour sur la fiche de l'animal</a></button>
        </form>
    </div>
    <?php include(__DIR__ . '/../templates/footer.php'); ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>