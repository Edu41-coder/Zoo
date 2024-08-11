<?php
require_once (__DIR__ . '/Database.php'); // Inclure la classe Database

class User
{
    private $conn; // Connexion à la base de données

    // Le constructeur accepte un argument pour la connexion à la base de données
    public function __construct(PDO $conn)
    {
        // Obtenir la connexion à la base de données singleton
        $this->conn = $conn;
    }

    // Méthode pour créer un nouvel utilisateur
    public function createUser($username, $password, $nom = null, $prenom = null, $role = null)
    {
        try {
            // Vérifier si le nom d'utilisateur existe déjà
            $stmt = $this->conn->prepare("SELECT * FROM utilisateur WHERE username = :username");
            $stmt->execute(['username' => $username]);
            if ($stmt->rowCount() > 0) {
                throw new Exception("Le nom d'utilisateur existe déjà.");
            }

            // Insérer le nouvel utilisateur sans hacher le mot de passe
            $stmt = $this->conn->prepare("INSERT INTO utilisateur (username, password, nom, prenom, role_id) VALUES (:username, :password, :nom, :prenom, :role)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':role', $role);
            $stmt->execute();

            return true; // Retourner true en cas de succès
        } catch (Exception $e) {
            // Afficher le message d'exception
            echo 'Erreur : ' . $e->getMessage();
            return false; // Retourner false en cas d'échec
        }
    }

    // Méthode pour obtenir un utilisateur par son ID
    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Méthode pour obtenir un utilisateur par son nom d'utilisateur
    public function getUserByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateur WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $user : false;
    }

    // Méthode pour obtenir le nom d'utilisateur par ID
    public function getUsernameById($id)
    {
        $stmt = $this->conn->prepare("SELECT username FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Méthode pour vérifier le mot de passe d'un utilisateur
    public function verifyPassword($username, $password)
    {
        $user = $this->getUserByUsername($username);
        if ($user) {
            echo "Utilisateur trouvé: " . print_r($user, true);
            if ($password === $user['password']) {
                return true;
            } else {
                echo "Mot de passe incorrect pour l'utilisateur: $username";
            }
        } else {
            echo "Utilisateur non trouvé: $username";
        }
        return false;
    }

    // Ajouter d'autres méthodes liées aux utilisateurs si nécessaire
}
?>