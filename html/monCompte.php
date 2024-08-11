<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../Php/verification_connexion.php');
require_once(__DIR__ . '/../classes/Database.php'); // Utiliser Database pour MySQL
require_once(__DIR__ . '/../classes/Database_Mongo.php'); // Utiliser Database_Mongo pour MongoDB
require_once(__DIR__ . '/../classes/Habitat.php');
require_once(__DIR__ . '/../classes/Zoo.php');
require_once(__DIR__ . '/../classes/Consultation.php');
require_once(__DIR__ . '/../classes/Avis.php');

// Page du site actuelle
$page = "compte";
$message = $_GET['message'] ?? '';

// Obtenir la connexion à la base de données MySQL via le singleton
$database = Database::getInstance();
$db = $database->connect();

// Obtenir la connexion à la base de données MongoDB via le singleton
$databaseMongo = Database_Mongo::getInstance();
$dbMongo = $databaseMongo->getBdd();

// Utilisation de la classe Zoo pour obtenir les heures d'ouverture et de fermeture
$zoo = new Zoo($db);
$heureOuverture = $zoo->getOpeningHours();
$heureFermeture = $zoo->getClosingHours();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon compte</title>
    <!-- Bootstrap 5.1 CSS -->
    <link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../styles/zoo.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    include("../templates/menu.php");
    if ($message == 'modifHorairesOk') {
        echo ('<div class="alert alert-success">Vous venez de modifier les horaires du ZOO.</div>');
    } else if ($message == 'erreurDansEnvoiFormulaire') {
        echo ('<div class="alert alert-danger">Une erreur a été rencontrée dans l\'envoi du formulaire.</div>');
    } else if ($message == "erreur1") {
        echo "<div class='alert alert-danger'>Ce pseudo est déjà utilisé, veuillez recommencer...</div>";
    } else if ($message == "erreur2") {
        echo "<div class='alert alert-danger'>Le mot de passe que vous avez choisi n'est pas bon, il doit faire 10 caractères au minimum, il doit avoir au moins une lettre majuscule et en plus il doit avoir au moins un caractère spécial, veuillez recommencer...</div>";
    } else if ($message == "erreur3") {
        echo "<div class='alert alert-danger'>Le rôle de l'inscription n'est pas correct, veuillez recommencer...</div>";
    } else if ($message == "erreur4") {
        echo "<div class='alert alert-danger'>Le mail doit être une adresse e-mail valide, veuillez recommencer...</div>";
    } else if ($message == "ok") {
        echo "<div class='alert alert-success'>Vous venez de créer un nouveau compte d'utilisateur. L'utilisateur vient de recevoir un mail pour se connecter.</div>";
    } else if ($message == "avisValide") {
        echo "<div class='alert alert-success'>L'avis vient d'être validé. Il est désormais visible sur le site.</div>";
    }else if ($message == "avisKo") {
        echo "<div class='alert alert-danger'>Échec de la validation de l'avis.</div>";}
     else if ($message == "suppressionOk") {
        echo "<div class='alert alert-success'>L'avis a été supprimé avec succès.</div>";
    } else if ($message == "suppressionFail") {
        echo "<div class='alert alert-danger'>Échec de la suppression de l'avis.</div>";
    }
    ?>
    <div class="container">
        <h1>Mon compte</h1>
        <?php if ($role == "admin") { ?>
            <p class="mt20perso mb20perso" id="menuAdmin">
                <a class="lienMonCompte" href="monCompte.php#horaires">Horaires du ZOO</a> |
                <a class="lienMonCompte" href="monCompte.php#inscription">Inscription d'un nouvel utilisateur</a> |
                <a class="lienMonCompte" href="monCompte.php#dashboard">Dashboard</a> |
                <a class="lienMonCompte" href="avis_animal.php">Avis des vétérinaires</a>
            </p>
            <h2 class="mt30perso" id="horaires">Horaires du ZOO</h2>
            <div>
                <form action="../Php/modif_horaires.php" method="POST">
                    <input type="hidden" name="typeForm" value="horaires">
                    <div class="form-group mt10perso">
                        <label for="heureDebut">Heure d'ouverture du zoo</label>
                        <input type="time" class="form-control" id="heureDebut" name="heureDebut" placeholder="Heure d'ouverture du zoo" value="<?php echo htmlspecialchars($heureOuverture); ?>" required>
                    </div>
                    <div class="form-group mt10perso">
                        <label for="heureFin">Heure de fermeture du zoo</label>
                        <input type="time" class="form-control" id="heureFin" name="heureFin" placeholder="Heure de fermeture du zoo" value="<?php echo htmlspecialchars($heureFermeture); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success mt10perso">Modifier les horaires</button>
                    <button class="btn btn-primary mt10perso"><a class="lien" href="../index.php">Voir les horaires sur la page d'accueil</a></button>
                </form>
            </div>
            <h2 class="mt30perso" id="inscription">Inscription d'un nouvel utilisateur</h2>
            <div>
                <form action="../Php/valid_inscription_depuis_admin.php" method="post">
                    <div class="form-group mt10perso">
                        <label for="pseudo">Mail</label>
                        <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Mail" required>
                    </div>
                    <div class="form-group mt10perso">
                        <label for="password">Mot de passe</label>
                        <input type="text" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                    </div>
                    <div class="form-group mt10perso">
                        <label for="nom">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="form-group mt10perso">
                        <label for="prenom">Prenom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prenom" required>
                    </div>
                    <div class="form-group mt10perso">
                        <label for="role">Rôle</label>
                        <div class="input-group mb-3">
                            <select class="form-control" name="role" id="role" required>
                                <option value="1">Vétérinaire</option>
                                <!-- option value="3">Administrateur</option -->
                                <option value="4">Employé</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt10perso">Inscrire un nouvel utilisateur</button>
                </form>
            </div>
            <h2 class="mt30perso" id="dashboard">Dashboard</h2>
            <div>
                <?php
                // Utilisation de la classe Consultation pour obtenir les consultations
                $consultation = new Consultation($dbMongo);
                $resultatsClique = $consultation->getConsultations();

                if (count($resultatsClique) > 0) {
                ?>
                    <table class="table table-striped mt10perso">
                        <thead>
                            <tr>
                                <th scope="col">Nom de l'animal</th>
                                <th scope="col">Nombre de cliques des visiteurs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultatsClique as $ligne) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ligne["prenom"]); ?></td>
                                    <td><?php echo htmlspecialchars($ligne["nbClique"]); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else {
                    echo "Aucune consultation d'animal n'a été enregistrée pour le moment.";
                } ?>
            </div>
        <?php } else if ($role == "veto") { ?>
            <div class="mt10perso">
                Il n'y a rien à voir ici ! Rendez-vous sur la liste des habitats pour écrire vos rapports.
            </div>
        <?php } else if ($role == "employe") { ?>
            <h2 class="mt30perso" id="horaires">Avis à valider</h2>
            <div class="mt10perso" id="divParentAvis">
                <?php
                // Utilisation de la classe Avis pour obtenir les avis non validés
                $avisObj = new Avis($db);
                $resultats3 = $avisObj->obtenirAvisNonValides();

                $nbAvisAValider = count($resultats3);

                if ($nbAvisAValider > 0) {
                    foreach ($resultats3 as $ligne3) {
                ?>
                        <div class="avisAValider mt10perso">
                            <div>
                                <?php echo htmlspecialchars($ligne3["pseudo"]); ?>
                                -
                                <?php echo htmlspecialchars($ligne3["commentaire"]); ?>
                            </div>
                            <button class="btn btn-success"><a class="lien" href="../Php/valid_avis_employe.php?id=<?php echo htmlspecialchars($ligne3["avis_id"]); ?>">Valider l'avis</a></button>
                            <form action="../Php/delete_avis.php" method="post" style="display:inline;">
                                <input type="hidden" name="avis_id" value="<?php echo htmlspecialchars($ligne3["avis_id"]); ?>">
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <p>Aucun avis sur le ZOO n'est à valider !</p>
                <?php
                }
                ?>
            </div>
        <?php } ?>
    </div>
    <?php
    include(__DIR__ . '/../templates/footer.php');
    ?>
</body>

</html>