<?php
// Assurer que la session est démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
    <title>Connexion - Zoo Arcadia</title>
</head>

<body>
    <?php
    // Définir la page actuelle pour le menu
    $page = "compte";
    include("../templates/menu.php");

    // Afficher les messages d'erreur de connexion
    $message = $_GET['message'] ?? '';
    if ($message == 'connexionKo1') {
        echo "<div class='alert alert-danger'>Erreur de connexion. Veuillez réessayer.</div>";
    } elseif ($message == 'connexionKo2') {
        echo "<div class='alert alert-danger'>Erreur de connexion. Veuillez réessayer.</div>";
    } elseif ($message == 'connexionKo') {
        echo "<div class='alert alert-danger'>Erreur de connexion. Veuillez réessayer.</div>";
    }
    ?>
    <div class="container">
        <h1>Connexion</h1>
        <form action="../Php/valid_connexion.php" method="post">
            <div class="form-group mt10perso">
                <label for="pseudo">Pseudo</label>
                <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Pseudo" required>
            </div>
            <div class="form-group mt10perso">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                <div class="lien2 mt10perso">
                    <a href="#" id="afficherMasquer">Afficher le mot de passe</a>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt10perso">Se connecter</button>
        </form>
    </div>

    <?php
    include(__DIR__ . '/../templates/footer.php');
    ?>

    <script>
        // Script pour afficher/masquer le mot de passe
        const passwordInput = document.getElementById('password');
        const toggleLink = document.getElementById('afficherMasquer');

        toggleLink.addEventListener('click', function(e) {
            e.preventDefault();
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleLink.textContent = 'Masquer le mot de passe';
            } else {
                passwordInput.type = 'password';
                toggleLink.textContent = 'Afficher le mot de passe';
            }
        });
    </script>

</body>

</html>