<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un habitat</title>

    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    require_once(__DIR__ . '/../Php/verification_connexion.php');
    require_once(__DIR__ . '/../classes/Database.php');
    require_once(__DIR__ . '/../classes/Habitat.php');
    require_once(__DIR__ . '/../classes/ImageHandler.php');

    // Obtenir la connexion à la base de données via le singleton
    $database = Database::getInstance();
    $db = $database->connect();
    $imageHandler = new ImageHandler($db);
    $image = new Image($db);
    $habitat = new Habitat($db, $imageHandler, $image);

    $page = 'habitat';
    include(__DIR__ . '/../templates/menu.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        header('Content-Type: application/json'); // Assurez-vous que le contenu est JSON
        try {
            $maxFileSize = 2 * 1024 * 1024; // 2MB

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
                throw new Exception('Erreur de téléchargement du fichier : ' . $habitat->codeToMessage($_FILES['image_id']['error']));
            }

            // Vérifier la taille de l'image
            ImageHandler::checkImageSize($_FILES['image_id'], $maxFileSize);

            // Vérifier si l'image existe déjà dans la base de données par son nom
            if ($imageHandler->imageExistsByName($_FILES['image_id']['name'])) {
                throw new Exception('Cette image existe déjà.');
            }

            // Ajouter l'habitat à la base de données
            $habitat->addHabitat($_POST['nom'], $_POST['description'], $_FILES['image_id']);

            echo json_encode(['message' => 'L\'habitat a été ajouté avec succès.']);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }
    ?>
    <div class="container">
        <h1>Ajouter un habitat</h1>
        <form id="habitatForm" enctype="multipart/form-data">
            <input type="hidden" id="hiddenMessage" value="">
            <div class="form-group mt10perso">
                <label for="nomHabitat">Nom</label>
                <input type="text" class="form-control" id="nomHabitat" name="nom" placeholder="Nom de l'habitat" required>
            </div>

            <div class="form-group mt10perso">
                <label for="descriptionHabitat">Description</label>
                <input type="text" class="form-control" id="descriptionHabitat" name="description" placeholder="Description de l'habitat" required>
            </div>
            <div class="form-group mt10perso">
                <label for="imageHabitat">Image</label>
                <div class="input-group mb-3">
                    <input type="file" class="form-control-file" id="imageHabitat" name="image_id" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Ajouter</button>
            <button class="btn btn-primary"><a class="lien" href="liste_habitat.php">Retour à la liste des habitats</a></button>
        </form>
        <div id="responseMessage" class="mt10perso"></div>
    </div>
    <?php
    include(__DIR__ . '/../templates/footer.php');
    ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
    <!-- Axios CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.getElementById('habitatForm').addEventListener('submit', async (event) => {
            event.preventDefault(); // Empêche le comportement par défaut du formulaire

            // Récupère les valeurs des champs du formulaire
            const formData = new FormData(event.target);

            try {
                // Envoie une requête POST avec Axios
                const response = await axios.post('ajouter_habitat.php', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Vérifie la réponse et met à jour le message caché
                if (response.data.message) {
                    document.getElementById('hiddenMessage').value = response.data.message;
                } else if (response.data.error) {
                    document.getElementById('hiddenMessage').value = response.data.error;
                }

                // Affiche le message dans l'élément responseMessage
                const message = document.getElementById('hiddenMessage').value || 'L\'habitat vient d\'être ajouté correctement.';
                document.getElementById('responseMessage').innerHTML = `<div class='alert alert-success'>${message}</div>`;
            } catch (error) {
                // Affiche un message d'erreur en cas de problème
                document.getElementById('responseMessage').innerHTML = `<div class='alert alert-danger'>Une erreur s'est produite.</div>`;
            }
        });
    </script>
</body>

</html>