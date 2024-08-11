<?php
require_once(__DIR__ . '/Database.php'); // Inclure la classe Database

class ImageHandler
{
    private $conn;

    // Le constructeur accepte un argument pour la connexion à la base de données
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Méthode statique pour télécharger une image
    public static function uploadImage($image, $uploadDir)
    {
        $fileTmpPath = $image['tmp_name']; // Chemin temporaire du fichier
        $fileName = $image['name']; // Nom du fichier
        $fileNameCmps = explode(".", $fileName); // Séparer le nom du fichier et son extension
        $fileExtension = strtolower(end($fileNameCmps)); // Obtenir l'extension du fichier en minuscules
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg'); // Extensions de fichiers autorisées

        // Vérifier si l'extension du fichier est autorisée
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Créer le répertoire de téléchargement s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // Vérifier si le répertoire de téléchargement est accessible en écriture
            if (!is_writable($uploadDir)) {
                throw new Exception('Le répertoire de destination n\'est pas accessible en écriture : ' . $uploadDir);
            }
            $dest_path = $uploadDir . $fileName; // Chemin de destination du fichier

            // Déplacer le fichier téléchargé vers le répertoire de destination
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                return $fileName; // Retourner le nom du fichier si le déplacement est réussi
            } else {
                throw new Exception('Erreur lors du déplacement du fichier téléchargé. Chemin de destination : ' . $dest_path . '. Erreur : ' . $image['error']);
            }
        } else {
            throw new Exception('Type de fichier invalide. Types autorisés : ' . implode(',', $allowedfileExtensions));
        }
    }

    // Méthode statique pour vérifier la taille de l'image
    public static function checkImageSize($image, $maxFileSize)
    {
        // Vérifier que la clé 'size' est présente dans l'array $image
        if (!isset($image['size'])) {
            throw new Exception('La clé size n\'est pas définie dans l\'array $image.');
        }
        // Vérifier si la taille de l'image dépasse la taille maximale autorisée
        if ($image['size'] > $maxFileSize) {
            throw new Exception('Veuillez choisir un fichier de taille inférieure à ' . ($maxFileSize / (1024 * 1024)) . ' Mo.');
        }
    }

    // Méthode pour vérifier si une image existe déjà par son nom
    public function imageExistsByName($imageName)
    {
        // Ajouter des logs pour déboguer
        error_log("Vérification de l'existence de l'image : " . $imageName);

        // Préparer et exécuter la requête pour vérifier si l'image existe déjà dans la base de données
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM image WHERE image_data = :imageName');
        $stmt->bindParam(':imageName', $imageName, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        // Ajouter des logs pour déboguer
        error_log("Nombre d'images trouvées : " . $count);

        // Retourner true si l'image existe, sinon false
        return $count > 0;
    }
}
?>