<?php
$message = $_GET['message'] ?? '';

// Inclusion des fichiers nécessaires
require_once(__DIR__ . '/../classes/Database.php');
require_once(__DIR__ . '/../classes/Habitat.php');
require_once(__DIR__ . '/../classes/Image.php');
require_once(__DIR__ . '/../Php/verification_connexion_simple.php');
$page = "habitat";

// Obtenir la connexion à la base de données singleton
$database = Database::getInstance();
$db = $database->connect();
$imageHandler = new ImageHandler($db);
$image=new Image($db);
$habitat = new Habitat($db, $imageHandler,$image);


$resultats = $habitat->getAllHabitats();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Liste des habitats</title>
	<!-- Bootstrap 5.1 CSS -->
	<link href="../styles/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="../styles/zoo.css" rel="stylesheet" type="text/css">
	<script>
		function confirmDeletion(habitatId) {
			if (confirm("Êtes-vous sûr de vouloir supprimer cet habitat ?")) {
				window.location.href = '../Php/suppr_habitat.php?id=' + habitatId;
			}
		}
	</script>
</head>

<body>
	<?php
	// Inclusion du menu
	include("../templates/menu.php");

	// Affichage des messages
	if ($message == 'ajoutOk') {
		echo "<div class='alert alert-success'>Habitat ajouté avec succès.</div>";
	} elseif ($message == 'modifOk') {
		echo "<div class='alert alert-success'>Habitat modifié avec succès.</div>";
	} elseif ($message == 'supprOk') {
		echo "<div class='alert alert-success'>Habitat supprimé avec succès.</div>";
	} elseif ($message == 'commentaireOk') {
		echo "<div class='alert alert-success'>Commentaire ajouté avec succès.</div>";
	} elseif ($message == 'ajoutFail') {
		echo "<div class='alert alert-danger'>Échec de l'ajout de l'habitat.</div>";
	} elseif ($message == 'modifFail') {
		echo "<div class='alert alert-danger'>Échec de la modification de l'habitat.</div>";
	} elseif ($message == 'supprFail') {
		echo "<div class='alert alert-danger'>Échec de la suppression de l'habitat.</div>";
	} elseif ($message == 'commentaireFail') {
		echo "<div class='alert alert-danger'>Échec de l'ajout du commentaire.</div>";
	} elseif ($message == 'ajoutAnimalOk') {
		echo "<div class='alert alert-success'>Animal ajouté avec succès.</div>";
	} elseif ($message == 'modifAnimalOk') {
		echo "<div class='alert alert-success'>Animal modifié avec succès.</div>";
	} elseif ($message == 'supprAnimalOk') {
		echo "<div class='alert alert-success'>Animal supprimé avec succès.</div>";
	} elseif ($message == 'ajoutAnimalFail') {
		echo "<div class='alert alert-danger'>Échec de l'ajout de l'animal.</div>";
	} elseif ($message == 'modifAnimalFail') {
		echo "<div class='alert alert-danger'>Échec de la modification de l'animal.</div>";
	} elseif ($message == 'supprAnimalFail') {
		echo "<div class='alert alert-danger'>Échec de la suppression de l'animal.</div>";
	} elseif ($message == 'imageExists') {
		echo "<div class='alert alert-warning'>L'image existe déjà.</div>";
	} elseif ($message == 'habitatNotEmpty') {
		echo "<div class='alert alert-danger'>Veuillez supprimer d'abord les animaux dans cet habitat.</div>";
	}
	?>
	<div class="container">
		<h1>Liste des habitats</h1>
		<?php if ($role == "admin") { ?>
			<div class="divDebut">
				<button type="button" class="btn btn-success btn-lg"><a class="lien" href="ajouter_habitat.php">Ajouter un habitat</a></button>
			</div>
		<?php } ?>
		<table class="table table-striped mt10perso">
			<thead>
				<tr>
					<th scope="col">Image</th>
					<th scope="col">Nom</th>
					<?php if ($role == "admin") { ?>
						<th>Modifier</th>
						<th>Supprimer</th>
					<?php } else if ($role == "veto") { ?>
						<th>Écrire un commentaire</th>
					<?php } ?>
					<th>Voir le détail de l'habitat</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($resultats as $ligne) {
					$resultatImage = $image->getHabitatImages($ligne["habitat_id"]);
					$imagePath = !empty($resultatImage) ? $resultatImage[0]["image_data"] : 'default.jpg';
				?>
					<tr>
						<td><img src="../image/uploads/<?php echo htmlspecialchars($imagePath); ?>" alt="Image"></td>
						<td><?php echo htmlspecialchars($ligne["nom"]); ?></td>
						<?php if ($role == "admin") { ?>
							<td><a href='modif_habitat.php?id=<?php echo htmlspecialchars($ligne["habitat_id"]); ?>&nom=<?php echo htmlspecialchars($ligne["nom"]); ?>&description=<?php echo htmlspecialchars($ligne["description"]); ?>&commentaire=<?php echo htmlspecialchars($ligne["commentaire_habitat"]); ?>'><i class="fa-solid fa-pen"></i></a></td>
							<td><a href="javascript:void(0);" onclick="confirmDeletion(<?php echo htmlspecialchars($ligne["habitat_id"]); ?>)"><i class="fa-solid fa-trash"></i></a></td>
						<?php } else if ($role == "veto") { ?>
							<td><a href='ecrire_commentaire_habitat.php?id=<?php echo htmlspecialchars($ligne["habitat_id"]); ?>&nom=<?php echo htmlspecialchars($ligne["nom"]); ?>'><i class="fa-solid fa-pen-nib"></i></a></td>
						<?php } ?>
						<td><a href="detail_habitat.php?id=<?php echo htmlspecialchars($ligne['habitat_id']); ?>"><i class="fa-solid fa-eye"></i></a></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php include("../templates/footer.php"); ?>
	<script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>