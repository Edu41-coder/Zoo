<?php
    // Inclusion de la vérification de connexion simple
    require_once(__DIR__ . '/../Php/verification_connexion_simple.php');
    // Inclusion de la classe Database
    require_once(__DIR__ . '/../classes/Database.php');

    // Page du site actuelle
    $page = "contact";
    $message = $_GET['message'] ?? '';

    // Obtenir la connexion à la base de données via le singleton
    $database = Database::getInstance();
    $db = $database->connect();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nous contacter</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
        // Inclusion du menu
        include(__DIR__ . '/../templates/menu.php');

        // Affichage des messages en fonction des paramètres GET
        if ($message == 'mailOk') {
            echo "<div class='alert alert-success'>Votre message a été envoyé avec succès.</div>";
        } elseif ($message == 'mailKo') {
            echo "<div class='alert alert-danger'>Une erreur s'est produite lors de l'envoi de votre message. Veuillez réessayer.</div>";
        } elseif ($message == 'okAvis') {
            echo "<div class='alert alert-success'>Votre avis a été envoyé avec succès.</div>";
        } elseif ($message == 'avisKo') {
            echo "<div class='alert alert-danger'>Une erreur s'est produite lors de l'envoi de votre avis. Veuillez réessayer.</div>";
        }
    ?>
    <div class="container">
        <h1>Nous contacter</h1>
        <form action="../Php/envoiMail2.php" method="post">
            <div class="form-group mt10perso">
                <label for="nom">Nom complet</label>
                <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom complet" required>
            </div>
            <div class="form-group mt10perso">
                <label for="mail">Mail</label>
                <input type="email" class="form-control" id="mail" name="mail" placeholder="Mail" required>
            </div>
            <div class="form-group mt10perso">
                <label for="subject">Sujet</label>
                <input type="text" class="form-control" id="subject" name="subject" placeholder="Sujet" required>
            </div>
            <div class="form-group mt10perso">
                <label for="message">Message</label>
                <textarea class="form-control" id="message" name="message" placeholder="Message" required></textarea>
            </div>
            <button type="submit" class="btn btn-success mt10perso">Envoyer</button>
        </form>
        <h1>Laisser un avis</h1>
        <form action="../Php/valid_avis.php" method="post">
            <div class="form-group mt10perso">
                <label for="pseudoAvis">Pseudo</label>
                <input type="text" class="form-control" id="pseudoAvis" name="pseudoAvis" placeholder="Pseudo" required>
            </div>
            <div class="form-group mt10perso">
                <label for="messageAvis">Message</label>
                <textarea class="form-control" id="messageAvis" name="messageAvis" placeholder="Votre avis" required></textarea>
            </div>
            <button type="submit" class="btn btn-success mt10perso">Envoyer</button>
        </form>
    </div>
    <?php
        // Inclusion du pied de page
        include(__DIR__ . '/../templates/footer.php');
    ?>
    <!-- Bootstrap Bundle with Popper -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>