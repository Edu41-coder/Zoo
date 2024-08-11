<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un service</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    // Inclusion des fichiers nécessaires
    require_once(__DIR__ . '/../Php/verification_connexion.php');
    require_once(__DIR__ . '/../classes/Database.php');
    require_once(__DIR__ . '/../classes/Service.php');

    // Obtenir la connexion à la base de données via le singleton
    $database = Database::getInstance();
    $db = $database->connect();
    $service = new Service($db);

    $page = 'service';
    include(__DIR__ . '/../templates/menu.php'); // Inclusion du menu

    // Récupérer les paramètres GET
    $id = $_GET['id'] ?? null;
    $nom = $_GET['nom'] ?? null;
    $description = $_GET['description'] ?? null;

    // Vérifier les permissions de l'utilisateur
    if ($role != "admin" && $role != "employe") {
        header('Location: liste_service.php');
        exit();
    }

    $message = '';
    $error = '';

    // Traiter le formulaire POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        header('Content-Type: application/json'); // Assurez-vous que le contenu est JSON
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $service->updateService($data['id'], $data['nom'], $data['description']);
            echo json_encode(['message' => 'Le service a été modifié avec succès.']);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }

    // Récupérer les détails du service
    $details = $service->getServiceDetails($id);
    ?>
    <div class="container">
        <h1>Modifier un service</h1>
        <form id="serviceForm">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <input type="hidden" id="hiddenMessage" value="">
            <div class="form-group mt10perso">
                <label for="nomService">Nom</label>
                <input type="text" class="form-control" id="nomService" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
            </div>
            <div class="form-group mt10perso">
                <label for="descriptionService">Description</label>
                <input type="text" class="form-control" id="descriptionService" name="description" value="<?php echo htmlspecialchars($description); ?>" required>
            </div>
            <button type="submit" class="btn btn-success mt10perso">Modifier</button>
            <button class="btn btn-primary mt10perso"><a class="lien" href="liste_service.php">Retour à la liste des services</a></button>
        </form>
        <div id="responseMessage" class="mt10perso"></div>
    </div>
    <?php include(__DIR__ . '/../templates/footer.php'); ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
    <!-- Axios CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.getElementById('serviceForm').addEventListener('submit', async (event) => {
            event.preventDefault(); // Empêche le comportement par défaut du formulaire

            // Récupère les valeurs des champs du formulaire
            const id = document.querySelector('input[name="id"]').value;
            const nom = document.querySelector('input[name="nom"]').value;
            const description = document.querySelector('input[name="description"]').value;

            try {
                // Envoie une requête POST avec Axios
                const response = await axios.post('modif_service.php', {
                    id: id,
                    nom: nom,
                    description: description
                }, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                // Vérifie la réponse et met à jour le message caché
                if (response.data.message) {
                    document.getElementById('hiddenMessage').value = response.data.message;
                } else if (response.data.error) {
                    document.getElementById('hiddenMessage').value = response.data.error;
                }

                // Affiche le message dans l'élément responseMessage
                const message = document.getElementById('hiddenMessage').value || 'Le service vient d\'être modifié correctement.';
                document.getElementById('responseMessage').innerHTML = `<div class='alert alert-success'>${message}</div>`;
            } catch (error) {
                // Affiche un message d'erreur en cas de problème
                document.getElementById('responseMessage').innerHTML = `<div class='alert alert-danger'>Une erreur s'est produite.</div>`;
            }
        });
    </script>
</body>

</html>