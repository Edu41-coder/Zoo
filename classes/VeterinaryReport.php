<?php
require_once (__DIR__ . '/Database.php'); // Inclure la classe Database

class VeterinaryReport
{
    private $conn; // Connexion à la base de données
    private $table = 'rapport_veterinaire'; // Nom de la table des rapports vétérinaires

    // Le constructeur accepte un argument pour la connexion à la base de données
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Méthode pour obtenir les rapports vétérinaires
    public function getReports($animal_id, $debut, $fin)
    {
        $requete = 'SELECT rv.*, a.prenom FROM ' . $this->table . ' rv
                    JOIN animal a ON rv.animal_id = a.animal_id';

        // Si l'ID de l'animal est vide, le définir à 0
        if (empty($animal_id)) {
            $animal_id = 0;
        }

        // Ajouter une condition WHERE en fonction de l'ID de l'animal
        if ($animal_id > 0) {
            $requete .= ' WHERE rv.animal_id = :animal_id';
        } else {
            $requete .= ' WHERE rv.animal_id > :animal_id';
        }

        // Ajouter une condition pour la plage de dates si les dates de début et de fin sont fournies
        if (!empty($debut) && !empty($fin)) {
            $requete .= " AND rv.date BETWEEN :debut AND :fin";
        }

        // Ajouter un ordre de tri par date décroissante
        $requete .= " ORDER BY rv.date DESC";

        // Préparer et exécuter la requête
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);

        // Lier les paramètres de date si fournis
        if (!empty($debut) && !empty($fin)) {
            $stmt->bindParam(':debut', $debut, PDO::PARAM_STR);
            $stmt->bindParam(':fin', $fin, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Méthode pour ajouter un avis vétérinaire
    public function addReview($animal_id, $detail, $etat)
    {
        // S'assurer que la session est démarrée
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si le nom d'utilisateur est défini dans la session
        if (!isset($_SESSION['utilisateur'])) {
            throw new Exception('Nom d\'utilisateur invalide.');
        }

        $username = $_SESSION['utilisateur'];

        // Vérifier si le nom d'utilisateur existe dans la table utilisateur
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM utilisateur WHERE username = :username');
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $userExists = $stmt->fetchColumn();

        if ($userExists == 0) {
            throw new Exception('Nom d\'utilisateur invalide.');
        }

        // Insérer l'avis dans la table rapport_veterinaire
        $requete = 'INSERT INTO ' . $this->table . ' (animal_id, detail, etatAvis, date, username) VALUES (:animal_id, :detail, :etat, NOW(), :username)';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->bindParam(':detail', $detail, PDO::PARAM_STR);
        $stmt->bindParam(':etat', $etat, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Méthode pour supprimer un avis vétérinaire
    public function deleteReview($avis_id)
    {
        $requete = 'DELETE FROM ' . $this->table . ' WHERE rapport_veterinaire_id = :avis_id';
        $stmt = $this->conn->prepare($requete);
        $stmt->bindParam(':avis_id', $avis_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>