<?php
require_once(__DIR__ . '/../Php/verification_connexion.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/Database_Mongo.php'); // Inclure la classe Database_Mongo
require_once(__DIR__ . '/../classes/Animal.php');
require_once(__DIR__ . '/../classes/ImageHandler.php');
require_once(__DIR__ . '/../classes/Image.php');

// Obtenir la connexion à la base de données MySQL via le singleton
$database = Database::getInstance();
$db = $database->connect();

// Instancier la classe Animal avec la connexion singleton
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

// Récupérer les détails de l'animal
$id = $_GET['id'];
$details = $animal->getAnimalDetails($id);

// Traiter le formulaire de mise à jour de l'animal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $animal->updateAnimal($id, $_POST['prenom'], $_POST['race'], $_POST['habitat']);
        header('Location: detail_habitat.php?id=' . $_POST['habitat']);
        exit();
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un animal</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="container">
        <h1>Modifier un animal</h1>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($details['animal_id']); ?>">
            <div class="form-group mt10perso">
                <label for="prenomAnimal">Prénom</label>
                <input type="text" class="form-control" id="prenomAnimal" name="prenom" value="<?php echo htmlspecialchars($details['prenom']); ?>" required>
            </div>
            <div class="form-group mt10perso">
                <label for="raceAnimal">Race de l'animal</label>
                <div class="input-group mb-3">
                    <select class="form-control" name="race" id="raceAnimal" required>
                        <?php
                        $races = $animal->getRaces();
                        foreach ($races as $race) {
                            $selected = ($details['race_id'] == $race['race_id']) ? "selected" : "";
                            echo ('<option ' . $selected . ' value="' . $race["race_id"] . '">' . $race["abel"] . '</option>');
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group ">
                <label for="habitatAnimal">Habitat de l'animal</label>
                <div class="input-group mb-3">
                    <select name="habitat" class="form-control" id="habitatAnimal" required>
                        <?php
                        $habitats = $animal->getHabitats();
                        foreach ($habitats as $habitat) {
                            $selected = ($details['habitat_id'] == $habitat['habitat_id']) ? "selected" : "";
                            echo ('<option ' . $selected . ' value="' . $habitat["habitat_id"] . '">' . $habitat["nom"] . '</option>');
                        }
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt10perso">Modifier</button>
            <button class="btn btn-primary mt10perso"><a class="lien" href="liste_habitat.php">Retour à la liste des habitats</a></button>
        </form>
    </div>
    <?php include(__DIR__ . '/../templates/footer.php'); ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>