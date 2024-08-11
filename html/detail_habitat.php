<?php

$message = $_GET['message'] ?? ''; // Récupérer le message de la requête GET
$page = "habitat";
require_once(__DIR__ . '/../Php/verification_connexion_simple.php');
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/Database_Mongo.php'); // Inclure la classe Database_Mongo
require_once(__DIR__ . '/../classes/Habitat.php');
require_once(__DIR__ . '/../classes/Animal.php');

// Obtenir la connexion à la base de données singleton
$database = Database::getInstance();
$db = $database->connect();

// Créer des instances des classes Habitat et Animal
$imageHandler = new ImageHandler($db);
$image = new Image($db);
$habitat = new Habitat($db, $imageHandler, $image);
$animal = new Animal($db, $imageHandler, $image);

$habitat_id = $_GET['id']; // Récupérer l'ID de l'habitat de la requête GET
$resultats = $habitat->getHabitatDetails($habitat_id); // Obtenir les détails de l'habitat
$resultatImage = $habitat->getHabitatImages($habitat_id); // Obtenir les images de l'habitat
$resultats2 = $animal->getAnimalsByHabitat($habitat_id); // Obtenir les animaux dans l'habitat

// Gestion de l'ajout d'image via un formulaire POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    try {
        $habitat->addHabitatImage($habitat_id, $_FILES['image']); // Ajouter l'image à l'habitat
        header('Location: detail_habitat.php?id=' . $habitat_id . '&message=ajoutOk'); // Rediriger avec un message de succès
        exit();
    } catch (Exception $e) {
        $message = $e->getMessage(); // Capturer le message d'erreur
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Détail d'un habitat</title>
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
    <script>
        function confirmDeletion(animalId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet animal ?")) {
                window.location.href = '../Php/suppr_animal.php?id=' + animalId;
            }
        }
    </script>
</head>

<body>
    <?php
    include("../templates/menu.php"); // Inclure le menu
    if ($message == 'ajoutOk') {
        echo ('<div class="alert alert-success">L\'image vient d\'être ajoutée avec succès !</div>');
    } elseif ($message == 'suppressionOk') {
        echo ('<div class="alert alert-success">L\'image vient d\'être supprimée avec succès !</div>');
    } elseif ($message) {
        echo ('<div class="alert alert-danger">' . htmlspecialchars($message) . '</div>');
    }
    ?>
    <div class="container">
        <?php if ($resultats) : ?>
            <h1>Détail de l'habitat <?php echo htmlspecialchars($resultats['nom']); ?></h1>
            <button class="btn btn-primary"><a class="lien" href="liste_habitat.php">Retour à la liste des habitats</a></button>
            <?php if ($role == "admin") { ?>
                <button class="btn btn-success btn-lg"><a class="lien" href="ajout_image_habitat.php?id=<?php echo $habitat_id; ?>&nom=<?php echo htmlspecialchars($resultats['nom']); ?>">Ajouter une image</a></button>
                <button class="btn btn-danger-light btn-lg"><a class="lien" href="supprimer_image_habitat.php?id=<?php echo $habitat_id; ?>&nom=<?php echo htmlspecialchars($resultats['nom']); ?>">Supprimer une image</a></button>
            <?php } ?>
            <?php if ($role == "employe") { ?>
                <div>
                    <h2>Commentaire de l'habitat</h2>
                    <p><?php echo htmlspecialchars($resultats['commentaire_habitat']); ?></p>
                </div>
            <?php } ?>
            <div id="divHabitatDetail">
                <?php if ($resultatImage) : ?>
                    <div id="carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach ($resultatImage as $index => $value) : ?>
                                <button type="button" data-bs-target="#carousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="true" aria-label="Slide <?php echo $index + 1; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner">
                            <?php foreach ($resultatImage as $index => $value) : ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="../image/uploads/<?php echo htmlspecialchars($value['image_data']); ?>" class="d-block w-100" alt="Image de l'habitat">
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
            </div>
            <h2>Animaux dans cet habitat</h2>
            <?php if ($role == "admin") { ?>
                <button class="btn btn-success btn-lg"><a class="lien" href="ajouter_animal.php?habitat_id=<?php echo $habitat_id; ?>">Ajouter un animal</a></button>
            <?php } ?>
            <?php if ($resultats2) : ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Prénom</th>
                            <th>État</th>
                            <th>Race</th>
                            <th>Voir</th>
                            <?php if ($role == "admin") { ?>
                                <th>Avis</th>
                                <th>Modifier</th>
                                <th>Supprimer</th>
                            <?php } else if ($role == "employe") { ?>
                                <th>Ajouter de la nourriture</th>
                            <?php } else if ($role == "veto") { ?>
                                <th>Consulter la nourriture donnée</th>
                                <th>Laisser un avis sur l'animal</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultats2 as $ligne) {
                            $resultatImagesAnimal = $animal->getAnimalImages($ligne["animal_id"]);
                            if (empty($ligne["etat"])) {
                                $ligne["etat"] = "Non défini";
                            }
                            // Vérifiez si le fichier image existe
                            $imagePath = "../image/uploads/default.jpg"; // Chemin vers une image par défaut
                            if (!empty($resultatImagesAnimal)) {
                                $imagePath = "../image/uploads/" . htmlspecialchars($resultatImagesAnimal[0]["image_data"]);
                                if (!file_exists($imagePath)) {
                                    echo "Image non trouvée : " . $imagePath . "<br>";
                                    $imagePath = "../image/uploads/default.jpg"; // Chemin vers une image par défaut
                                }
                            }
                        ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Image"></td>
                                <td><?php echo htmlspecialchars($ligne["prenom"]); ?></td>
                                <td><?php echo htmlspecialchars($ligne["etat"]); ?></td>
                                <td><?php echo htmlspecialchars($ligne["abel"]); ?></td>
                                <td><a href="detail_animal.php?id=<?php echo htmlspecialchars($ligne["animal_id"]); ?>"><i class="fa-solid fa-eye"></i></a></td>
                                <?php if ($role == "admin") { ?>
                                    <td><a href="avis_animal.php?id=<?php echo htmlspecialchars($ligne["animal_id"]); ?>"><i class="fa-solid fa-eye"></i></a></td>
                                    <td><a href='modif_animal.php?id=<?php echo urlencode($ligne["animal_id"]); ?>&amp;prenom=<?php echo urlencode($ligne["prenom"]); ?>&amp;etat=<?php echo urlencode($ligne["etat"]); ?>&amp;race=<?php echo urlencode($ligne["race_id"]); ?>&amp;habitat=<?php echo urlencode($ligne["habitat_id"]); ?>'><i class="fa-solid fa-pen"></i></a></td>
                                    <td><a href="javascript:void(0);" onclick="confirmDeletion(<?php echo htmlspecialchars($ligne["animal_id"]); ?>)"><i class="fa-solid fa-trash"></i></a></td>
                                <?php } else if ($role == "employe") { ?>
                                    <td><a href='ajout_nourriture_animal.php?id=<?php echo htmlspecialchars($ligne["animal_id"]); ?>&prenom=<?php echo htmlspecialchars($ligne["prenom"]); ?>'><i class="fa-solid fa-plus"></i></a></td>
                                <?php } else if ($role == "veto") { ?>
                                    <td><a href='consulter_nourriture_animal.php?id=<?php echo htmlspecialchars($ligne["animal_id"]); ?>&prenom=<?php echo htmlspecialchars($ligne["prenom"]); ?>'><i class="fa-solid fa-eye"></i></a></td>
                                    <td><a href='laisser_avis_animal.php?id=<?php echo htmlspecialchars($ligne["animal_id"]); ?>&prenom=<?php echo htmlspecialchars($ligne["prenom"]); ?>'><i class="fa-solid fa-pen-nib"></i></a></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="mt10perso">Aucun animal ne vit dans cet habitat.</div>
            <?php endif; ?>
        <?php else : ?>
            <div class="alert alert-danger">Aucun habitat trouvé avec cet ID.</div>
        <?php endif; ?>
    </div>
    <?php include("../templates/footer.php"); ?>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>