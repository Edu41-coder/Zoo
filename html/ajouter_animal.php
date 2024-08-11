<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un animal</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    // Inclusion des fichiers nécessaires
    require_once(__DIR__ . '/../Php/verification_connexion.php');
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
    include(__DIR__ . '/../templates/menu.php');

    // Vérifier si l'utilisateur est un administrateur
    if ($role != "admin") {
        header('Location: liste_habitat.php');
        exit();
    }

    // Définir la taille maximale de fichier en octets (par exemple, 8 Mo)
    $maxFileSize = 8 * 1024 * 1024; // 8 Mo

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Vérifier si la taille du fichier dépasse la limite de post_max_size
        if (empty($_FILES) && empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
            echo "<div class='alert alert-danger'>Veuillez choisir un fichier de taille inférieure à " . ($maxFileSize / (1024 * 1024)) . " Mo.</div>";
        } else {
            try {
                // Vérifier la taille du fichier avant de traiter le téléchargement
                ImageHandler::checkImageSize($_FILES['image_id'], $maxFileSize);

                // Vérifier si l'image existe déjà dans la base de données par son nom
                if ($imageHandler->imageExistsByName($_FILES['image_id']['name'])) {
                    throw new Exception('Cette image existe déjà.');
                }

                // Messages de débogage
                echo "<pre>";
                echo "Répertoire de téléchargement : " . realpath(__DIR__ . '/../image/uploads') . "\n";
                echo "Est accessible en écriture : " . (is_writable(realpath(__DIR__ . '/../image/uploads')) ? "Oui" : "Non") . "\n";
                echo "Chemin de destination : " . realpath(__DIR__ . '/../image/uploads') . '/' . basename($_FILES['image_id']['name']) . "\n";
                echo "Chemin temporaire du fichier : " . $_FILES['image_id']['tmp_name'] . "\n";
                echo "Nom du fichier : " . $_FILES['image_id']['name'] . "\n";
                echo "Taille du fichier : " . $_FILES['image_id']['size'] . "\n";
                echo "Erreur du fichier : " . $_FILES['image_id']['error'] . "\n";
                echo "Le fichier temporaire existe : " . (file_exists($_FILES['image_id']['tmp_name']) ? "Oui" : "Non") . "\n";
                echo "Le fichier temporaire est lisible : " . (is_readable($_FILES['image_id']['tmp_name']) ? "Oui" : "Non") . "\n";
                echo "Le fichier temporaire est téléchargé : " . (is_uploaded_file($_FILES['image_id']['tmp_name']) ? "Oui" : "Non") . "\n";
                echo "</pre>";

                // Vérifier les erreurs de téléchargement
                if ($_FILES['image_id']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('Erreur de téléchargement du fichier : ' . $animal->codeToMessage($_FILES['image_id']['error']));
                }

                // Ajouter des logs pour déboguer
                error_log("Ajout de l'animal avec l'image : " . $_FILES['image_id']['name']);

                // Ajouter l'animal à la base de données
                $animal->addAnimal($_POST['prenom'], $_POST['etat'], $_POST['race'], $_POST['habitat'], $_FILES['image_id']);
                echo "<div class='alert alert-success'>Animal ajouté avec succès.</div>";
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
            }
        }
    }

    $races = $animal->getRaces();
    $habitats = $animal->getHabitats();
    $currentHabitatId = $_GET['id'] ?? null;

    // Trier les races alphabétiquement par 'abel'
    usort($races, function ($a, $b) {
        return strcmp($a['abel'], $b['abel']);
    });

    // Trier les habitats alphabétiquement par 'nom'
    usort($habitats, function ($a, $b) {
        return strcmp($a['nom'], $b['nom']);
    });
    ?>
    <div class="container">
        <h1>Ajouter un animal</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group mt10perso">
                <label for="prenomAnimal">Prénom</label>
                <input type="text" class="form-control" id="prenomAnimal" name="prenom" placeholder="Prénom de l'animal" required>
            </div>
            <div class="form-group mt10perso">
                <label for="etatAnimal">État</label>
                <input type="text" class="form-control" id="etatAnimal" name="etat" placeholder="État de l'animal" required>
            </div>
            <div class="form-group mt10perso">
                <label for="raceAnimal">Race de l'animal</label>
                <div class="input-group mb-3">
                    <select class="form-control" name="race" id="raceAnimal" required>
                        <?php
                        foreach ($races as $race) {
                            echo ('<option value="' . $race["race_id"] . '">' . $race["abel"] . '</option>');
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="habitatAnimal">Habitat de l'animal</label>
                <div class="input-group mb-3">
                    <select name="habitat" class="form-control" id="habitatAnimal" required>
                        <?php
                        foreach ($habitats as $habitat) {
                            $selected = ($currentHabitatId == $habitat["habitat_id"]) ? "selected" : "";
                            echo ('<option value="' . $habitat["habitat_id"] . '" ' . $selected . '>' . $habitat["nom"] . '</option>');
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="imageAnimal">Image</label>
                <div class="input-group mb-3">
                    <input type="file" class="form-control-file" id="imageAnimal" name="image_id" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Ajouter</button>
            <a class="btn btn-primary" href="liste_habitat.php">Retour à la liste des habitats</a>
        </form>
    </div>
    <?php include(__DIR__ . '/../templates/footer.php'); ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>