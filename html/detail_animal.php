<?php

require_once(__DIR__ . '/../Php/verification_connexion_simple.php');
require_once(__DIR__ . '/../classes/Database.php'); // Utiliser Database pour MySQL
require_once(__DIR__ . '/../classes/Database_Mongo.php'); // Utiliser Database_Mongo pour MongoDB
require_once(__DIR__ . '/../classes/Animal.php');
require_once(__DIR__ . '/../classes/Consultation.php');

// Obtenir la connexion à la base de données MySQL via le singleton
$database = Database::getInstance();
$db = $database->connect();
// Obtenir la connexion à la base de données MongoDB via le singleton
$databaseMongo = Database_Mongo::getInstance();
$dbMongo = $databaseMongo->getBdd(); // Utiliser getBdd() pour obtenir la connexion MongoDB


// Créer une instance de la classe Animal avec la connexion à la base de données MySQL et MongoDB
$imageHandler = new ImageHandler($db);
$image = new Image($db);
$animal = new Animal($db, $imageHandler, $image);

// Récupérer l'ID de l'animal depuis les paramètres GET
$animal_id = $_GET['id'];

// Récupérer les détails de l'animal et les images associées
$details = $animal->getAnimalDetails($animal_id);
$images = $animal->getAnimalImages($animal_id);

// Vérifier si les détails de l'animal ont été récupérés avec succès
if ($details === false) {
    echo "Erreur : Impossible de récupérer les détails de l'animal.";
    exit();
}

// Vérifier si les images de l'animal ont été récupérées avec succès
if ($images === false) {
    echo "Erreur : Impossible de récupérer les images de l'animal.";
    exit();
}

// Mettre à jour les consultations si l'utilisateur n'est pas connecté
if (!isset($_SESSION['utilisateur'])) {
    $consultation = new Consultation($dbMongo);
    $consultation->updateConsultation($animal_id, $details['prenom']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Détail de l'animal</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
    <style>
        .btn-danger-light {
            background-color: #ff6666;
            border-color: #ff6666;
            color: #000;
        }

        .btn-danger-light:hover {
            background-color: #ff3333;
            border-color: #ff3333;
        }
    </style>
</head>

<body>
    <?php include(__DIR__ . '/../templates/menu.php'); ?>
    <div class="container">
        <h1>Détail de l'animal <?php echo htmlspecialchars($details['prenom']); ?></h1>
        <button class="btn btn-primary"><a class="lien"
                href="detail_habitat.php?id=<?php echo htmlspecialchars($details['habitat_id']); ?>">Retour détail
                habitat</a></button>
        <?php if ($role == "admin") { ?>
            <button class="btn btn-success btn-lg"><a class="lien"
                    href="ajout_image_animal.php?id=<?php echo $animal_id; ?>&nom=<?php echo htmlspecialchars($details['prenom']); ?>">Ajouter
                    une image</a></button>
            <button class="btn btn-danger-light btn-lg"><a class="lien"
                    href="supprimer_image_animal.php?id=<?php echo $animal_id; ?>&nom=<?php echo htmlspecialchars($details['prenom']); ?>">Supprimer
                    une image</a></button>
        <?php } ?>
        <div id="divAnimalDetail">
            <?php if ($images): ?>
                <div id="carousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php foreach ($images as $index => $value): ?>
                            <button type="button" data-bs-target="#carousel" data-bs-slide-to="<?php echo $index; ?>"
                                class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="true"
                                aria-label="Slide <?php echo $index + 1; ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($images as $index => $value): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="../image/uploads/<?php echo htmlspecialchars($value['image_data']); ?>"
                                    class="d-block w-100" alt="Image de l'animal">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            <?php endif; ?>
            <div>
                <span class="boldSpan">État de l'animal :</span>
                <br><?php echo htmlspecialchars($details['etat']); ?>
                <br><br>
                <span class="boldSpan">Race de l'animal :</span>
                <br><?php echo htmlspecialchars($details['abel']); ?>
                <br><br>
                <span class="boldSpan">Habitat de l'animal :</span>
                <br><?php echo htmlspecialchars($details['nom']); ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__ . '/../templates/footer.php'); ?>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>