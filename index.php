<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zoo Arcadia</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="./styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="./styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    $page = 'accueil';
    require_once(__DIR__ . '/Php/verification_connexion_simple.php');
    require_once(__DIR__ . '/classes/Database.php');
    require_once(__DIR__ . '/classes/Database_Mongo.php'); // Inclure la classe Database_Mongo
    require_once(__DIR__ . '/classes/Zoo.php');
    require_once(__DIR__ . '/classes/Habitat.php');
    require_once(__DIR__ . '/classes/Animal.php');
    require_once(__DIR__ . '/classes/Service.php');
    require_once(__DIR__ . '/classes/Avis.php');
    require_once(__DIR__ . '/helpers.php');
    include(__DIR__ . '/templates/menu.php');

    // Utilisation de la connexion à la base de données avec le pattern singleton
    $database = Database::getInstance();
    $db = $database->connect();

    // Utilisation de la classe Zoo pour obtenir les heures d'ouverture et de fermeture
    $zoo = new Zoo($db);
    
    $heureOuverture = $zoo->getOpeningHours();
    $heureFermeture = $zoo->getClosingHours();
    
    // Afficher les messages d'erreur de connexion
    $message = $_GET['message'] ?? '';
    if ($message == 'connexionKo1') {
        echo "<div class='alert alert-danger'>Erreur de connexion. Veuillez réessayer.</div>";
    } elseif ($message == 'connexionKo2') {
        echo "<div class='alert alert-danger'>Erreur de connexion. Veuillez réessayer.</div>";
    } elseif ($message == 'connexionKo') {
        echo "<div class='alert alert-danger'>Erreur de connexion. Veuillez réessayer.</div>";
    } elseif ($message == 'connexionOk') {
        echo "<div class='alert alert-success'>Connexion réussie.</div>";
    }
    elseif ($message == 'deconnexionOk') {
        echo "<div class='alert alert-success'>Déconnexion réussie.</div>";
    }
    // Formatage des heures
    $heureOuvertureFormattee = DateTime::createFromFormat('H:i', $heureOuverture)->format('G\hi');
    $heureFermetureFormattee = DateTime::createFromFormat('H:i', $heureFermeture)->format('G\hi');

    // Définition du fuseau horaire et récupération de l'heure actuelle
    date_default_timezone_set('Europe/Paris');
    $heure_actuelle = date('H:i');

    // Détermination du statut d'ouverture du zoo
    if ($heure_actuelle >= $heureOuverture && $heure_actuelle < $heureFermeture) {
        $status = "Votre ZOO est actuellement <span style='color: green; font-weight: bold;'>ouvert</span> de $heureOuvertureFormattee à $heureFermetureFormattee";
    } else {
        $status = "Votre ZOO est actuellement <span style='color: red; font-weight: bold;'>fermé</span>. Ouverture chaque jour de $heureOuvertureFormattee à $heureFermetureFormattee.";
    }
    ?>
    <div class="container" id="homePage">
        <h1>Bienvenue sur la page du zoo Arcadia !</h1>
        <p><?php echo $status; ?></p>
        <div id="carousel" class="carousel slide">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="image/uploads/1.jpg" class="d-block w-100" alt="Image de présentation du zoo 1">
                </div>
                <div class="carousel-item">
                    <img src="image/uploads/2.jpg" class="d-block w-100" alt="Image de présentation du zoo 2">
                </div>
                <div class="carousel-item">
                    <img src="image/uploads/3.webp" class="d-block w-100" alt="Image de présentation du zoo 3">
                </div>
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
        <div class='mt10perso'>
            <p>
                Arcadia est un zoo situé en France près de la forêt de Brocéliande, en Bretagne depuis 1960. Plus de soixante ans plus tard, il se distingue toujours et fait partie des 5 premiers parcs zoologiques français en nombre de visiteurs. Au travers de la qualité de ses installations et le travail de ses équipes, le parc porte toute son attention et ses efforts au développement du bien-être des animaux et à l’accueil des visiteurs.
                Le zoo est entièrement indépendant au niveau des énergies et représente les valeurs de l’écologie dont nous sommes fier.
                <br>
                <br>
                Votre ZOO est ouvert chaque jour de <?php echo $heureOuvertureFormattee; ?> à <?php echo $heureFermetureFormattee; ?>.
            </p>
        </div>
        <div id="habitats">
            <h2 class="mt30perso">Habitats du zoo</h2>

            <?php
            // Utilisation de la classe Habitat pour obtenir les habitats
            $imageHandler = new ImageHandler($db);
            $image = new Image($db);
            $habitat = new Habitat($db, $imageHandler, $image);
            $resultats = $habitat->getAllHabitatsLim();

            foreach ($resultats as $ligne) {
                // Récupération des images associées aux habitats
                $resultatImage = $habitat->getHabitatImages($ligne["habitat_id"]);
                $imagePath = !empty($resultatImage) ? 'image/uploads/' . $resultatImage[0]["image_data"] : 'image/default.jpg';
            ?>
                <div class="ligneHabitat d-flex align-items-center">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Image de l'habitat" class="img-fluid me-3">
                    <div class="contenuHabitatsHomePage">
                        <p class='pHomePage pHomePageElement1'><?php echo htmlspecialchars($ligne["nom"]); ?></p>
                        <p class='pHomePage'><?php echo htmlspecialchars($ligne["description"]); ?></p>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <div id="services">
            <h2 class="mt30perso">Services du zoo</h2>
            <?php
            // Utilisation de la classe Service pour obtenir les services
            $service = new Service($db);
            $resultats2 = $service->getAllServices();

            foreach ($resultats2 as $ligne2) {
            ?>
                <h4><?php echo htmlspecialchars($ligne2["nom"]); ?></h4>
                <p><?php echo htmlspecialchars($ligne2["description"]); ?></p>
            <?php
            }
            ?>
        </div>
        <div id="animaux">
            <h2 class="mt30perso">Animaux du zoo</h2>
            <?php
            // Utilisation de la classe Animal pour obtenir les animaux
            $animal = new Animal($db, $imageHandler, $image);
            $resultats3 = $animal->getAllAnimals();

            foreach ($resultats3 as $ligne3) {
                // Récupération des images associées aux animaux
                $resultatImageAnimal = $animal->getAnimalImages($ligne3["animal_id"]);
                $imagePathAnimal = !empty($resultatImageAnimal) ? 'image/uploads/' . $resultatImageAnimal[0]["image_data"] : 'image/default.jpg';
            ?>
                <div class="ligneHabitat d-flex align-items-center">
                    <img class="imgAnimalIndex img-fluid me-3" src="<?php echo htmlspecialchars($imagePathAnimal); ?>" alt="Image de l'animal">
                    <div class="contenuHabitatsHomePage">
                        <p class='pHomePage pHomePageElement1'><?php echo htmlspecialchars($ligne3["prenom"]); ?></p>
                        <p class='pHomePage'><?php echo htmlspecialchars($ligne3["abel"]); ?></p>
                        <p class='pHomePage'><?php echo htmlspecialchars($ligne3["nom"]); ?></p>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <div id="avis">
            <h2 class="mt30perso">Les 5 derniers avis sur le zoo</h2>
            <?php
            // Utilisation de la classe Avis pour obtenir les avis
            $avis = new Avis($db);
            $resultats4 = $avis->getLatestAvis();

            foreach ($resultats4 as $ligne4) {
            ?>
                <h4><?php echo htmlspecialchars($ligne4["pseudo"]); ?></h4>
                <p><?php echo htmlspecialchars($ligne4["commentaire"]); ?></p>
            <?php
            }
            ?>
        </div>
    </div>
    <?php
    include(__DIR__ . '/templates/footer.php');
    ?>
    <script src="./js/bootstrap.bundle.min.js"></script>
</body>

</html>